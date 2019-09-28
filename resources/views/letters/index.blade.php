<html>
<head>
  <style>
    @page { margin: 100px 25px; }
    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }
    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }
  </style>
</head>
<body>
  <header>
    <div align="right">
      <img width="100" height="100" src="images/pms-logo.png">
    </div>
  </header>
  <footer>
    <div align="center">
      306, Corporate Center, Nirmal Lifestyle, L.B.S. Road, Mulund (W), Mumbai - 400 080
      <br>
      Tel. +91 22 6164 3400, Email id: hr@pousse.in, Website: www.pousse.in
      <br>
      CIN: U74140MH2014PTC251903
    </div>
  </footer>
  <main>
    <br><br>
    @yield('letter')
    <img src="https://pmsallcdn.s3.ap-south-1.amazonaws.com/documentation/authorized-signatory.png" style="width: 350px; height: 100px;">
  </main>
</body>
</html>
