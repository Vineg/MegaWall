<!DOCTYPE html>
<html>
        <head>
                <title>Chat - Servlet 3</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <script type="text/javascript" src="/files/jscripts/jq.js"></script>
                <script type="text/javascript" src="/files/jscripts/jqs.js"></script>
                <script type="text/javascript">
                $.stream.setup({enableXDR: true});
                
                $(function() {
                        $.stream("http://localhost:8080/SimpleStreamServ/chat", {
                                type: "http",
                                dataType: "json",
                                context: $("#content")[0],
                                open: function(event, stream) {
                                        $("#editor .message").removeAttr("disabled").focus();
                                        //stream.send({username: chat.username, message: "Hello"});
                                },
                                message: function(event) {
                                        alert(eval('function q(){return "qwe";}q();'));
                                },
                                error: function() {
                                        $("#editor .message").attr("disabled", "disabled");
                                },
                                close: function() {
                                        $("#editor .message").attr("disabled", "disabled");
                                }
                        });
                        
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
        <script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript"></script>
				<script type="text/javascript">
				  VK.init({
				    apiId: 2379536,
				    nameTransportPath: "/test/xd_receiver.php"
				  });
				VK.Api.call('users.search', {q: "sex:2"}, function(r) {
					  if(r.response) {
					    alert(JSON.stringify(r.response[1]));
					  }
					});
				</script>
        </body>
</html>