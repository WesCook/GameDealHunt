<?php require "script.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>GameDeal Hunt</title>
	<meta name="robots" content="noindex,nofollow">
	<link rel="stylesheet" type="text/css" href="/assets/style.css">
</head>
<body>

<div class="wrapper">
	<?php if ($error) { ?>
		<h1>Something went wrong!</h1>
		<p><?=$message;?></p>
		<p>If you're still having trouble you can contact us <a href="https://www.reddit.com/message/compose?to=%2Fr%2FGameDeals&amp;subject=GameDeals%20Hunt%20Problem" target="_blank">here</a>.</p>
	<?php } else { ?>

		<?php if ($receivedGame) { ?>
			<h1>Lookin' Good</h1>
			<p><?=$message;?></p>
			<hr>
			<div class="center">
				<h2><?=$gameName;?></h2>
				Steam Key: <input type="text" value="<?=$gameKey;?>" onClick="this.select();" readonly>
				<small><a href="steam://open/activateproduct">Redeem</a></small>
			</div>
			<hr>
			<p><small>This can be copied to Steam to redeem the game above.  If you have any problems, please contact us <a href="https://www.reddit.com/message/compose?to=%2Fr%2FGameDeals&amp;subject=GameDeals%20Hunt%20Problem" target="_blank">here</a>.</small></p>
			<p>Many thanks to those who donated their game keys for this giveaway.  Still, remember that it's the journey and not the destination.  We put these puzzles together as a fun Easter egg hunt for the /r/GameDeals community and hope you've enjoyed!</p>
		<?php } else { ?>

			<h1>Sorry!</h1>
			<p><?=$message;?></p>
		<?php } ?>
	<?php } ?>
</div>

</body>
</html>
