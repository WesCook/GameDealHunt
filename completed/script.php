<?php

/*
Overview of logic
-----------------
Connect to DB
Get reddit token
Get username
Check if user exists in users table
	If not, insert user into table
Check if user has claimed game
	If yes, show their key
	If no, ask if any keys left
		If yes, request key and mark as used
		If no, show sorry message
*/

//Disable error reporting
error_reporting(0);

// Globals
$redditAccountYoungest = 1428107600; // Unix timestamp of date reddit account needs to predate
$message = ""; // Output message
$error = false; // If error, bail out and display message

// Main program
// Only run next step if no error detected
if (!$error) $mysqli = connectDB("host", "user", "pass", "name"); // Enter your database credentials here
if (!$error) $redditData = connectReddit();
if (!$error) $userID = getUserID($mysqli, $redditData);
if (!$error) $response = checkForUsersGame($mysqli, $userID);

// If game received, get name/key
if (!$error && $receivedGame = $response["receivedGame"])
{
	$gameName = $response["gameName"];
	$gameKey = $response["gameKey"];
}


// Connect to database
function connectDB($host, $user, $pass, $database)
{
	global $message;
	global $error;

	$mysqli = new mysqli($host, $user, $pass, $database);
	if ($mysqli->connect_errno) // Error checking
	{
		$message = "We can't reach the database.  Probable cause is reddit hug of death.";
		$error = true;
		return false;
	}

	return $mysqli;
}

// Get reddit token and data
function connectReddit()
{
	global $message;
	global $error;
	global $redditAccountYoungest;

	require_once("reddit-php-sdk/reddit.php");
	$reddit = new reddit();
	if (!$reddit) // Error checking
	{
		$message = "Cannot access reddit account.";
		$error = true;
		return false;
	}

	// Get reddit data
	$redditDataObj = $reddit->getUser(); // Includes reddit account data such as account name and age
	if (!is_object($redditDataObj)) // Error checking
	{
		// Something went wrong.  This seems to happen sometimes.  It seems to be due a failed response from reddit.
		$message = "We're calling reddit but they're not answering.<br><br><strong>Often times <a href='verify.php'>a refresh</a> fixes this.</strong>";
		$error = true;
		return false;
	}

	// Convert from object into array
	$redditData = get_object_vars($redditDataObj);

	// Reddit account age check
	if ($redditData["created_utc"] > $redditAccountYoungest)
	{
		$message = "Well this is awkward...  Your account isn't actually old enough to qualify for a prize.<br>We hope you still enjoyed the puzzles and will join us for future events!";
		$error = true;
		return false;
	}

	return $redditData;
}

// Will search for user ID in database, and generate new user if not found
function getUserID($mysqli, $redditData)
{
	global $message;
	global $error;

	// Check if user is in users table
	$stmt = $mysqli->prepare("SELECT id FROM users WHERE name = ?");
	$stmt->bind_param("s", $redditData["name"]);
	$stmt->execute();
	$stmt->store_result();

	// Get user ID if found in table
	$userID = -1;
	if ($stmt->num_rows === 1)
	{
		// Get ID from database
		$stmt->bind_result($userID);
		$stmt->fetch();
	}
	$stmt->free_result();
	$stmt->close();

	// If user not found, add them
	if ($userID === -1)
		$userID = createUser($mysqli, $redditData); // Returns user ID

	// If user ID still -1 somehow, show an error.
	if ($userID === -1)
	{
		$message = "Cannot access user ID.";
		$error = true;
		return false;
	}

	// Return ID
	return $userID;
}

// Add user to database and return ID
function createUser($mysqli, $redditData)
{
	// Insert user into database
	$stmt = $mysqli->prepare("INSERT INTO users (name, reddit_created, hunt_created, ip) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", $redditData["name"], $redditData["created_utc"], time(), $_SERVER['REMOTE_ADDR']); // Insert reddit name, creation date, current timestamp, and client IP
	$stmt->execute();
	$userID = $stmt->insert_id; // Get ID from insertion
	$stmt->close();

	// Return ID from insertion
	return $userID;
}

// Check if user has claimed a game key.  Requests generation if not.
function checkForUsersGame($mysqli, $userID)
{
	global $message;

	// Check if user already has game assigned
	$usersGame = getUsersGame($mysqli, $userID);

	// Check how many games are left
	$gamesLeft = getGamesRemaining($mysqli);

	// If user has game already
	if ($usersGame)
	{
		$message = "You completed the puzzles in time and got a fancy new game as a reward.  Score!";
		return array("receivedGame" => true, "gameName" => $usersGame["name"], "gameKey" => $usersGame["key"]);
	}
	else
	{
		// If there are, request a new key
		if ($gamesLeft > 0)
		{
			$usersGame = requestNewGame($mysqli, $userID); // Request new game
			$gamesLeft--; // Subtract one due to the game we just claimed
			$message = "You completed the puzzles in time and got a fancy new game as a reward.  Score!";
			return array("receivedGame" => true, "gameName" => $usersGame["name"], "gameKey" => $usersGame["key"]);
		}
		else
		{
			// Else show sorry message
			$message = "Oh fiddlesticks, there aren't any games left!<br><br>We hope you still enjoyed the puzzles, and will join us again in the future.";
			return array("receivedGame" => false);
		}
	}
}

// Check if user already has game assigned.  Returns array with name/key if found, or false if not
function getUsersGame($mysqli, $userID)
{
	// Search key table for user
	$stmt = $mysqli->prepare("SELECT `name`,`key` FROM `keys` WHERE claimed_id = ?");
	$stmt->bind_param("i", $userID);
	$stmt->execute();
	$stmt->store_result();

	// If game already claimed by user, get that name/key
	$gameFound = false;
	if ($stmt->num_rows === 1)
	{
		// Get game name and key
		$stmt->bind_result($gameName, $gameKey);
		$stmt->fetch();
		$gameFound = true;
	}
	$stmt->free_result();
	$stmt->close();

	// Return data
	if ($gameFound)
		return array("name" => $gameName, "key" => $gameKey);
	else
		return false;
}

// Check if any games left
function getGamesRemaining($mysqli)
{
	// Search key table for user
	$stmt = $mysqli->prepare("SELECT claimed_id FROM `keys` WHERE claimed_id = 0"); // Get all rows without an assigned user
	$stmt->execute();
	$stmt->store_result();
	$remaining = $stmt->num_rows; // Get number of games left
	$stmt->free_result();
	$stmt->close();

	return $remaining;
}

// Claim new game
function requestNewGame($mysqli, $userID)
{
	// Update row as claimed
	$stmt = $mysqli->prepare("UPDATE `keys` SET claimed_id = ?, claimed_time = ? WHERE claimed_id = 0 LIMIT 1"); // Update first unclaimed row
	$stmt->bind_param("is", $userID, time()); // Updating with user ID and current timestamp
	$stmt->execute();
	$stmt->free_result();
	$stmt->close();

	// Get game name/key from row we just claimed
	$usersGame = getUsersGame($mysqli, $userID);

	return $usersGame;
}

?>