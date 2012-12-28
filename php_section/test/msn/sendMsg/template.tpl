<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>SendMsg Usage Example</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
  </head>

  <body>

    <h1>SendMsg Usage Example</h1>

    {INFO}

    <form action="index.php" method="post">

    <h3>Sender Information</h3>

    <table cellpadding="2" cellspacing="2" width="600" border="0">
      <tr>
        <td width="40%"><b>Sign-in name</b></td>
        <td width="60%"><input type="text" name="sender" size="40"{SIGNINNAME} /></td>
      </tr>
      <tr>
        <td><b>Password</b></td>
        <td><input type="password" name="password" size="40" /></td>
      </tr>
    </table>

    <h3>Message Information</h3>

    <table cellpadding="2" cellspacing="2" width="600" border="0">
      <tr>
        <td width="40%"><b>Recipient</b></td>
        <td width="60%"><input type="text" name="recipient" size="40"{RECIPIENT} /></td>
      </tr>
      <tr>
        <td><b>Message text</b></td>
        <td><input type="text" name="message" size="40"{MESSAGE} /></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="send" value="Send" /></td>
      </tr>
    </table>

    </form>

  </body>

</html>