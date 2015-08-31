<?php if (!defined('W3TC')) die(); ?>
<html>
	<head></head>
	<body>
        <p>
            Date: <?php echo date('m/d/Y H:i:s'); ?><br />
            Version: <?php echo W3TC_VERSION; ?><br />
            URL: <a href="<?php echo esc_attr($url); ?>"><?php echo htmlspecialchars($url); ?></a><br />
            Name: <?php echo htmlspecialchars($name); ?><br />
            E-Mail: <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo htmlspecialchars($email); ?></a><br />

            <?php if ($twitter): ?>
            Twitter: <a href="http://twitter.com/<?php echo esc_attr($twitter); ?>"><?php echo htmlspecialchars($twitter); ?></a><br />
            <?php endif; ?>

            <?php if ($phone): ?>
            Phone: <?php echo htmlspecialchars($phone); ?><br />
            <?php endif; ?>

            <?php if ($forum_url): ?>
            Forum Topic URL: <a href="<?php echo esc_attr($forum_url); ?>"><?php echo htmlspecialchars($forum_url); ?></a><br />
            <?php endif; ?>

            <?php if ($request_data_url): ?>
            Request data: <a href="<?php echo esc_attr($request_data_url); ?>"><?php echo htmlspecialchars($request_data_url); ?></a><br />
            <?php endif; ?>

            Subject: <?php echo htmlspecialchars($subject); ?>
        </p>

        <p>
            <?php echo nl2br(htmlspecialchars($description)); ?>
        </p>

        <hr />

        <font size="-1" color="#ccc">
            E-mail sent from IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?><br />
            User Agent: <?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?>
        </font>
    </body>
</html>
