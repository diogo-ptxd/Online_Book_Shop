<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="./assets/images/bookwise_logo.png" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.3/socket.io.min.js"></script>
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
  include "./templates/header.php"; ?>
  <script>
    const socket = io("http://localhost:3000/", {
      withCredentials: true,
    });

    // LISTEN FROM SERVER IF DATA SEND
    socket.on("sendallMSG", (p) => {
      alert("new message : " + p.data);
      console.log("new message " + p.data); // alert other client
      listdata.insertAdjacentHTML("afterend", "<br/>" + p.data);
    });
  </script>
</body>

</html>