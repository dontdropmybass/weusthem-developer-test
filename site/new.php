<?php

?>
<!doctype html>
<html>
<head>
    <script src="/js/spamwords.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/bootstrap.min.css"/>
    <script src="/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/css/styles.css"/>
</head>
<body>
    <div id="header">
        Emails <a href="#" id='new-email'><i class='material-icons'>create</i></a>
    </div>
    <div id="links">
        <a id="to-inbox" href="inbox.php">Inbox</a>
        <a id="to-sent" href="sent.php">Sent</a>
        <a id="to-archive" href="archive.php">Archive</a>
    </div>
    <div id="main">
        <form id="new-message" class="row col-sm-12" action="email.php" method="POST">
            <label for="to" class="col-sm-1">To:</label>
            <div class="col-sm-3 form-group">
                <input class="form-control" id="to" name="to" type="text" value="receiver@example.com"/>
            </div>
            <label for="from" class="col-sm-1"></label>
            <div class="col-sm-3 form-group">
                <input class="form-control" id="from" name="from" type="text" value="<?php echo $_GET['email'];?>" style="display:none;"/>
            </div>
            <div class="col-sm-4 form-group">

            </div>
            <label for="subject" class="col-sm-1">Subject:</label>
            <div class="form-group col-sm-11">
                <input class="form-control" name="subject" id="subject"/>
            </div>
            <textarea class="col-sm-12 form-control" style="height:300px;" name="body" id="body"></textarea>
            <a class="form-control" href="#" id="send" onclick="$('#new-message').submit();"><i class="material-icons">send</i></a>
        </form>
    </div>
    <script>
        $(function() {

        });
    </script>
</body>
</html>
