# GameDeal Hunt

## History

GameDealHunt was a series of puzzles created for Easter 2015 for the /r/GameDeals community.  It was devised and built in just under a week, and offered Steam keys to anybody that could complete one of the three challenge branches.

While the actual puzzles are implemented as simple HTML documents, the reddit account verification and key distribution code may be useful for others looking to implement a similar system.  The answers to the puzzles are of course included too.

This project uses [Reddit PHP SDK](https://github.com/jcleblanc/reddit-php-sdk) to connect to reddit.

## Install

To setup your own instance of GameDeal Hunt, you'll need a webserver with PHP and MySQL enabled.

### Configuration

* Under /completed/reddit-php-sdk/config.php, set $ENDPOINT_OAUTH_REDIRECT and $CLIENT_ID, and $CLIENT_SECRET to your [reddit API keys](https://www.reddit.com/prefs/apps/).
* Under /completed/script.php, set $redditAccountYoungest to the reddit age required to take advantage of the giveaway.  Configure your own database credentials under connectDB().
* This project includes an SQL file to import an empty set of users/keys to get started.  Import this into your database.

## Answers

Spoiler warning!  This section (as well as the /trivia, /computerscience, and /math directories) include the answers to the puzzles.  Only read on if you're okay with being spoiled.

### Trivia

<details>
	<summary>1</summary>
	<p>index.html</p>
</details>

<details>
	<summary>2</summary>
	<p>2009.html</p>
</details>

<details>
	<summary>3</summary>
	<p>katademo.html</p>
</details>

<details>
	<summary>4</summary>
	<p>meta.html</p>
</details>

<details>
	<summary>5</summary>
	<p>charity.html</p>
</details>

<details>
	<summary>6</summary>
	<p>bundlestars.html</p>
</details>

<details>
	<summary>7</summary>
	<p>tony.html</p>
</details>

<details>
	<summary>8</summary>
	<p>expired.html</p>
</details>

### Computer Science

<details>
	<summary>1</summary>
	<p>index.html</p>
	<p>Answer is visible in console tab of dev tools.</p>
</details>

<details>
	<summary>2</summary>
	<p>warmingup.html</p>
	<p>Answer is only visible from mobile devices.  Can be simulated using browser dev tools.</p>
</details>

<details>
	<summary>3</summary>
	<p>missingapiece.html</p>
	<p>Apply ROT13 cypher.</p>
</details>

<details>
	<summary>4</summary>
	<p>allyourbase.html</p>
	<p>Page is encoded as base64 string.  Can be pasted in URL bar to view.</p>
</details>

<details>
	<summary>5</summary>
	<p>Answer is the time as represented in a binary clock.</p>
</details>

<details>
	<summary>6</summary>
	<p>123952.html</p>
	<p>Answer has been appended as text file to dino.png.  Visible by extracting using zip tool, read in hex editor, or simply viewed into notepad.</p>
</details>

<details>
	<summary>7</summary>
	<p>scaredycat.html</p>
</details>

### Mathematics

<details>
	<summary>1</summary>
	<p>index.html</p>
</details>

<details>
	<summary>2</summary>
	<p>8625.html</p>
</details>

<details>
	<summary>3</summary>
	<p>coprime.html</p>
</details>

<details>
	<summary>4</summary>
	<p>55.html</p>
</details>

<details>
	<summary>5</summary>
	<p>3min.html</p>
</details>

<details>
	<summary>6</summary>
	<p>62436.html</p>
</details>
