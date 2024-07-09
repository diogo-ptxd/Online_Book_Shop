<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="icon"
      type="image/x-icon"
      href="./assets/images/bookwise_logo.png"
    />
    <title>BookWise</title>
    <style>
      body {
        margin: 0;
        padding: 0;
      }

      iframe {
        width: 100%;
        border: none;
        /* Removes borders */
        display: block;
        height: 100vh;
        /* Ensures iframes are displayed as blocks */
      }

      #header-frame {
        height: 100vh;
        /* Fixed height for the header */
      }
    </style>
  </head>

  <body>
    <!-- <iframe
      id="header-frame"
      src="./templates/header.php"
      title="header"
    ></iframe> -->

    <?php
    include "./templates/header.php";?>
  </body>
</html>