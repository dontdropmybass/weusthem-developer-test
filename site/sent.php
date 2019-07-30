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
        Emails <a href="new.php" id='new-email'><i class='material-icons'>create</i></a>
    </div>
    <div id="links">
        <a id="to-inbox" href="inbox.php">Inbox</a>
        <a id="to-sent" href="sent.php">Sent</a>
        <a id="to-archive" href="archive.php">Archive</a>
    </div>
    <div id="main">
        <div id="searchbar" class="row col-sm-12">
            <label for="to" class="col-sm-1">Receiver:</label>
            <div class="col-sm-3 form-group">
                <input class="form-control" id="to" name="to" type="text"/>
            </div>
            <label for="from" class="col-sm-1" style="display:none;">Sender:</label>
            <div class="col-sm-3 form-group" style="display:none;">
                <input class="form-control" id="from" name="from" type="text"/>
            </div>
            <label for="archived" class="col-sm-1">Archived?</label>
            <div class="col-sm-1 form-group">
                <input id="archived" name="archived" type="checkbox"/>
            </div>
            <div class="col-sm-2 form-group">
                <a href="#" id="search"><i class="material-icons">search</i></a>
            </div>
        </div>
        <div id="enter-email" style="width:40%;margin:0 auto;text-align:center;">
            LOGIN:
            <input class="form-control" id="email-address" name="email-address" type="text" value="receiver@example.com"/>
            <button class="form-control" id="go" style="background-color:#aaffaa">GO</button>
            <span class="form-control" id="error" style="display:none;color:red;font-weight:bold;">Please Enter An Email!</span>
        </div>
        <ul id="email-list" class='col-sm-12 row'>

        </ul>
    </div>
    <script>
        $(function() {
            $("#go").click(function() {
                if ($("#email-address").val() !== "") {
                    $("#from").val($("#email-address").val());
                    $("#new-email").attr('href', 'new.php?email='+$("#email-address").val());
                    $("#to-inbox").attr('href', 'inbox.php?email='+$("#email-address").val());
                    $("#to-archive").attr('href', 'archive.php?email='+$("#email-address").val());
                    $("#to-sent").attr('href', 'sent.php?email='+$("#email-address").val());
                    $("#enter-email").hide();
                    search();
                }
                else {
                    $("#error").show();
                }
            });
            <?php if (isset($_GET['email'])) { ?>
                $("#email-address").val("<?php echo $_GET['email']; ?>");
                $("#go").click();
                search();
            <?php } ?>
        });
        function search() {
            let url = "email.php?archived="+($("#archived").prop("checked") ? "1" : "0");
            if ($("#to").val() !== "") {
                url += "&to="+$("#to").val();
            }
            if ($("#from").val() !== "") {
                url += "&from="+$("#from").val();
            }
            console.log(url);
            $.ajax({
                url: url,
                success: function(result) {
                    $("#email-list").html("");
                    let json = JSON.parse(result);
                    let results = json["emails"];
                    for (let i = 0; i < Object.keys(results).length; i++) {
                        let key = Object.keys(results)[i];
                        let text =
                        "<li id='email-"+i+"' class='col-sm-12 row'>"+
                            "<div class='col-sm-10' style='font-weight:bold;'>Subject: "+
                            results[key]["subject"]+
                            "</div>"+
                            "<div class='col-sm-2'>";
                            if (results[key]["archived"]==1) {
                                text += "<a href='#' id='archive-"+i+"'><i class='material-icons'>archive</i></a>";
                            }
                            else {
                                text += "<a href='#' id='unarchive-"+i+"'><i class='material-icons'>unarchive</i></a>";
                            }
                            text += "<a href='#' id='delete-"+i+"'><i class='material-icons'>delete_forever</i></a>"+
                            "</div>"+
                            "<div class='col-sm-5'>From: "+
                            results[key]["from"]+
                            "</div>"+
                            "<div class='col-sm-5'>To: "+
                            results[key]["to"]+
                            "</div>"+
                            "<div class='col-sm-2'>Sent: "+
                            results[key]["created_at"]+
                            "</div>"+
                            "<div class='col-sm-12'>"+results[key]["body"]+"</div>"+
                        "</li>";
                        $("#email-list").append(text);
                        $("#delete-"+i).click(function() {
                            $.ajax({
                                url: "email.php",
                                type: 'DELETE',
                                data: {'id': results[key]["id"]},
                                contentType: 'application/json',
                                success: function(results) {
                                    $("#email-"+i).remove();
                                }
                            });
                        });
                        $("#archive-"+i+", #unarchive-"+i).click(function() {
                            let field = this;
                            $.ajax({
                                url: "email.php",
                                type: 'PUT',
                                data: {'id': results[key]["id"]},
                                contentType: 'application/json',
                                success: function(results) {
                                    if ($(field).attr("id").includes("unarchive")) {
                                        $(field).attr("id", "archive-"+i);
                                        $(field).html("<i class='material-icons'>archive</i>");
                                    }
                                    else {
                                        $(field).attr("id", "unarchive-"+i);
                                        $(field).html("<i class='material-icons'>unarchive</i>");
                                    }
                                    $("#email-"+i).remove();
                                },
                                error: function(a, b, c) {
                                    console.log(a, b, c);
                                }
                            });
                        });
                    }
                }
            });
        }
    </script>
</body>
</html>
