<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">

  <title>@yield('title', $subject ?? '')</title>
  <style>
    /* حطّ CSS inline-friendly هنا أو استخدم CSS Inliner قبل الارسال */
    body {
      margin: 0;
      padding: 0;
      font-family: Tahoma, Arial;
      background: #f6f6f6;
    }

    .container {
      max-width: 620px;
      margin: auto;
      background: #fff;
      padding: 24px
    }

    a {
      text-decoration: none
    }
  </style>
</head>

<body style="margin:0;padding:0;background:#f6f9fc;font-family:Arial,Helvetica,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f6f9fc">
    <tr>
      <td align="center" style="padding:24px">
        <div class="container">
          @yield('content')
        </div>
      </td>
    </tr>
  </table>
</body>

</html>