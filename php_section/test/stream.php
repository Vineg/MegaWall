<!DOCTYPE html>
<html>
        <head>
                <title>Chat - Servlet 3</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <script type="text/javascript" src="http://d.megawall.ru/files/jscripts/jq.js"></script>
                <script type="text/javascript" src="http://d.megawall.ru/files/jscripts/jqs.js"></script>
                <script type="text/javascript">
                $.stream.setup({enableXDR: true});
                
                var chat = {
                        lastUsername: "Donghwan Kim",
                        username: $.trim(window.prompt("Username?")) || "Anonymous" + $(window).width()
                };
                
                $(function() {
                        $.stream("http://d.megawall.ru:8080/SimpleStreamServ/chat", {
                                type: "http",
                                dataType: "json",
                                context: $("#content")[0],
                                open: function(event, stream) {
                                        $("#editor .message").removeAttr("disabled").focus();
                                        stream.send({username: chat.username, message: "Hello"});
                                },
                                message: function(event) {
                                        if (chat.lastUsername !== event.data.username) {
                                                $("<p />").addClass("user").text(chat.lastUsername = event.data.username).appendTo(this);
                                        }
                                        
                                        $("<p />").addClass("message").text(event.data.message).appendTo(this);
                                        this.scrollTop = this.scrollHeight;
                                },
                                error: function() {
                                        $("#editor .message").attr("disabled", "disabled");
                                },
                                close: function() {
                                        $("#editor .message").attr("disabled", "disabled");
                                }
                        });
                        
                        $("#editor .user").text(chat.username);
                        $("#editor .message").keyup(function(event) {
                                if (event.which === 13 && $.trim(this.value)) {
                                        $.stream().send({username: chat.username, message: this.value});
                                        this.value = "";
                                }
                        });
                        
                        $(window).resize(function() {
                                var content = $("#content").height($(window).height() - $("#editor").outerHeight(true) - 15)[0];
                                content.scrollTop = content.scrollHeight;
                        }).resize();
                });
                </script>
                <style>
                body {padding: 0; margin: 0; min-width: 320px; font-family: 'Trebuchet MS','Malgun Gothic',Verdana,Helvetica,Arial,sans-serif; font-size: 62.5%; color: #333333}
                .content {height: 100%; overflow-y: auto; padding: 14px 15px 0 25px;}
                .content p {margin: 0; padding: 0;}
                .content .user {font-size: 1.8em; color: #3e3e3e; font-weight: bold; letter-spacing: -1px; margin-top: 0.5em;}
                .content .message {font-size: 1.3em; color: #444444; line-height: 1.7em; word-wrap: break-word;}
                .editor {margin: 0 25px 15px 25px;}
                .editor .user {font-size: 1.5em; display: inline-block; margin: 1em;}
                .editor input {font-family: 'Trebuchet MS','Malgun Gothic',Verdana,Helvetica,Arial,sans-serif;}
                .editor .message {width: 100%; height: 28px; line-height: 28px; border: medium none; border-color: #E5E5E5 #DBDBDB #D2D2D2; border-style: solid; border-width: 1px;}
                </style>
        </head>
        <body>
                <div id="content" class="content">
                        <p class="user"><span>Donghwan Kim</span></p>
                        <p class="message">Welcome to jQuery Stream!</p>
                </div>
                <div id="editor" class="editor">
                        <p class="user"></p>
                        <form action="#" onsubmit="return false;">
                                <input class="message" type="text" disabled="disabled" />
                        </form>
                </div>
        </body>
</html>
