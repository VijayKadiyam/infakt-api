<!DOCTYPE html><html>
<head>
  <style type="text/css">
    table, tr, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    td {
      padding: 5px;
    }

    .yellow {
      background-color: lightyellow;
    }

    .cyan {
      background-color: lightcyan;
    }

    /* ==================== BAR GRAPH  ==================== */
    /* ------ container ------ */
    div.css_bar_graph
      {
      width: 490px;
      height: 320px;
      padding: 40px 20px 10px 70px;
      /* --- font --- */
      font-size: 13px;
      font-family: arial, sans-serif;
      font-weight: normal;
      color: #444444;
      background-color: #ffffff;
      position: relative; 
      margin-left: auto;
      margin-right: auto; 
      border: 1px solid #d5d5d5;
      /* --- css3 --- */
      border-radius: 10px;
      -webkit-border-radius: 10px;
      -moz-border-radius: 10px;
      }
      
    /* ------ hyperlinks ------ */
    div.css_bar_graph a
      {
      color: #444444;
      text-decoration: none;
      }
      
    /* ------ lists ------ */
    div.css_bar_graph ul
      {
      margin: 0px;
      padding: 0px;
      list-style-type: none;
      }
      
    div.css_bar_graph li
      {
      margin: 0px;
      padding: 0px;
      }

    /* ==================== Y-AXIS LABELS ==================== */
    /* ------ base ------ */
    div.css_bar_graph ul.y_axis
      {
      width: 50px;
      position: absolute;
      top: 40px;
      left: 10px;
      text-align: right;
      }
      
    /* ------ labels ------ */
    div.css_bar_graph ul.y_axis li
      {
      width: 100%;
      height: 49px; /* 50px including border */
      float: left;
      color: #888888;
      /* --- alignment correction --- */
      border-top: 1px solid transparent;
      position: relative;
      top: -13px; /* value = font height */
      } 
      
    /* ==================== X-AXIS LABELS  ==================== */
    /* ------ base ------ */
    div.css_bar_graph ul.x_axis
      {
      width: 100%;
      height: 50px;
      position: absolute;
      bottom: 0px;
      left: 90px;
      text-align: center;
      }
      
    /* ------ labels ------ */
    div.css_bar_graph ul.x_axis li
      {
      display: inline;
      width: 90px;
      float: left;
      }
      
    /* ==================== GRAPH LABEL  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.label
      {
      width: 100%;
      height: 50px;
      float: left;
      margin-top: 20px;
      text-align: center;
      }
      
    div.css_bar_graph div.label span
      {
      font-weight: bold;
      }
      
    /* ==================== GRAPH  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph
      {
      width: 100%;
      height: 100%;
      float: left;
      }
      
    /* ------ grid ------ */
    div.css_bar_graph div.graph ul.grid
      {
      width: 100%;
      }

    /* ------ IE grid ------ */
    div.css_bar_graph div.graph li
      {
      width: 100%;
      height: 49px; /* 50px including border */
      float: left;
      border-top: 1px solid #e5e5e5;
      } 
      
    /* ------ other browsers grid ------ */
    div.css_bar_graph div.graph li:nth-child(odd)
      {
      width: 100%;
      height: 49px; /* 50px including border */
      float: left;
      border-top: 1px solid #e5e5e5;
      background-color: #f8f8f8;
      }
      
    div.css_bar_graph div.graph li:nth-child(even)
      {
      width: 100%;
      height: 49px; /* 50px including border */
      float: left;
      border-top: 1px solid #e5e5e5;
      }
      
    /* ------ bottom grid element ------ */
    div.css_bar_graph div.graph li.bottom
      {
      border-top: 1px solid #d5d5d5;
      background-color: #eeeeee;
      height: 19px;
      }
      
    /* ==================== BARS COMMON  ==================== */
    /* ------ common styles ------ */
    div.css_bar_graph div.graph li.bar
      {
      width: 50px;
      float: left;
      position: absolute;
      bottom: 70px;
      text-align: center;
      /* --- css3 --- */
      /* --- transitions --- */
      -webkit-transition: all 0.15s ease-in-out;
      -moz-transition: all 0.15s ease-in-out;
      -o-transition: all 0.15s ease-in-out;
      -ms-transition: all 0.15s ease-in-out;
      transition: all 0.15s ease-in-out;
      }
      
    /* ------ bar top circle ------ */
    div.css_bar_graph div.graph li.bar div.top
      {
      width: 100%;
      height: 20px;
      margin-top: -10px;
      /* --- css3 --- */
      -moz-border-radius: 50px/20px;
      -webkit-border-radius: 50px 20px;
      border-radius: 50px/20px;
      /* --- transitions --- */
      -webkit-transition: all 0.15s ease-in-out;
      -moz-transition: all 0.15s ease-in-out;
      -o-transition: all 0.15s ease-in-out;
      -ms-transition: all 0.15s ease-in-out;
      transition: all 0.15s ease-in-out;
      }
      
    /* ------ bar bottom circle ------ */
    div.css_bar_graph div.graph li.bar div.bottom
      {
      width: 100%;
      height: 20px;
      position: absolute;
      bottom: -10px;
      left: 0px;
      /* --- css3 --- */
      -moz-border-radius: 50px/20px;
      -webkit-border-radius: 50px 20px;
      border-radius: 50px/20px;
      /* --- transitions --- */
      -webkit-transition: all 0.15s ease-in-out;
      -moz-transition: all 0.15s ease-in-out;
      -o-transition: all 0.15s ease-in-out;
      -ms-transition: all 0.15s ease-in-out;
      transition: all 0.15s ease-in-out;
      }

    /* ------ bar top label ------ */
    div.css_bar_graph div.graph li.bar span
      {
      position: relative;
      top: -50px;
      padding: 3px 5px 3px 5px;
      z-index: 100;
      background-color: #eeeeee;
      border: 1px solid #bebebe;
      /* --- css3 --- */
      border-radius: 3px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      /* --- gradient --- */
      background-image: linear-gradient(top, #ffffff, #f1f1f1 1px, #ebebeb); /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f1f1f1', endColorstr='#ebebeb'); /* IE5.5 - 7 */
      -ms-filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f1f1f1', endColorstr='#ebebeb'); /* IE8 */
      background: -ms-linear-gradient(top, #ffffff, #f1f1f1 1px, #ebebeb); /* IE9 */
      background: -moz-linear-gradient(top, #ffffff, #f1f1f1 1px, #ebebeb); /* Firefox */ 
      background: -o-linear-gradient(top, #ffffff, #f1f1f1 1px, #ebebeb); /* Opera 11  */
      background: -webkit-linear-gradient(top, #ffffff, #f1f1f1 1px, #ebebeb); /* Chrome 11  */
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #ffffff), color-stop(0.05, #f1f1f1), color-stop(1, #ebebeb)); /* Chrome 10, Safari */
      /* --- shadow --- */
      text-shadow: 0px 1px 0px rgba(255,255,255,1);
      box-shadow: 0px 1px 0px rgba(0,0,0,0.1);
      -webkit-box-shadow: 0px 1px 0px rgba(0,0,0,0.1);
      -moz-box-shadow: 0px 1px 0px rgba(0,0,0,0.1);
      /* --- transitions --- */
      -webkit-transition: all 0.15s ease-in-out;
      -moz-transition: all 0.15s ease-in-out;
      -o-transition: all 0.15s ease-in-out;
      -ms-transition: all 0.15s ease-in-out;
      transition: all 0.15s ease-in-out;
      }
      
    /* ------ bars positions ------ */
    div.css_bar_graph div.graph li.nr_1
      {
      left: 110px;
      }
      
    div.css_bar_graph div.graph li.nr_2
      {
      left: 200px;
      }
      
    div.css_bar_graph div.graph li.nr_3
      {
      left: 290px;
      }
      
    div.css_bar_graph div.graph li.nr_4
      {
      left: 380px;
      }
      
    div.css_bar_graph div.graph li.nr_5
      {
      left: 470px;
      }
      
    div.css_bar_graph div.graph li.nr_6
      {
      left: 560px;
      }
      
    div.css_bar_graph div.graph li.nr_7
      {
      left: 650px;
      }
      
    div.css_bar_graph div.graph li.nr_8
      {
      left: 740px;
      }

    div.css_bar_graph div.graph li.nr_9
      {
      left: 830px;
      }

    div.css_bar_graph div.graph li.nr_10
      {
      left: 920px;
      }

    /* ==================== BLUE BAR  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph li.blue
      {
      background: #208faf;  /* --- IE --- */
      background: rgba(32,143,175,0.8);
      }

    /* ------ top ------ */
    div.css_bar_graph div.graph li.blue div.top
      {
      background: #72b8cc;
      }
      
    /* ------ bottom ------ */
    div.css_bar_graph div.graph li.blue div.bottom
      {
      background: #208faf;
      }
      
    /* ==================== GREEN BAR  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph li.green
      {
      background: #608d00;  /* --- IE --- */
      background: rgba(96,141,0,0.8);
      }

    /* ------ top ------ */
    div.css_bar_graph div.graph li.green div.top
      {
      background: #a2c656;
      }
      
    /* ------ bottom ------ */
    div.css_bar_graph div.graph li.green div.bottom
      {
      background: #608d00;
      }
      
    /* ==================== ORANGE BAR  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph li.orange
      {
      background: #ff9000;  /* --- IE --- */
      background: rgba(255,144,0,0.8);
      }

    /* ------ top ------ */
    div.css_bar_graph div.graph li.orange div.top
      {
      background: #ffc24c;
      }
      
    /* ------ bottom ------ */
    div.css_bar_graph div.graph li.orange div.bottom
      {
      background: #ff9000;
      }
      
    /* ==================== PURPLE BAR  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph li.purple
      {
      background: #7d47ba;  /* --- IE --- */
      background: rgba(125,71,186,0.8);
      }

    /* ------ top ------ */
    div.css_bar_graph div.graph li.purple div.top
      {
      background: #b592dd;
      }
      
    /* ------ bottom ------ */
    div.css_bar_graph div.graph li.purple div.bottom
      {
      background: #7d47ba;
      }
      
    /* ==================== RED BAR  ==================== */
    /* ------ base ------ */
    div.css_bar_graph div.graph li.red
      {
      background: #d23648;  /* --- IE --- */
      background: rgba(210,54,72,0.8);
      }

    /* ------ top ------ */
    div.css_bar_graph div.graph li.red div.top
      {
      background: #ea828e;
      }
      
    /* ------ bottom ------ */
    div.css_bar_graph div.graph li.red div.bottom
      {
      background: #d23648;
      }
      
    /* ==================== HOVERS  ==================== */
    div.css_bar_graph div.graph li.blue:hover
      {
      cursor: pointer;
      background: #208faf;
      }
      
    div.css_bar_graph div.graph li.green:hover
      {
      cursor: pointer;
      background: #608d00;
      }
      
    div.css_bar_graph div.graph li.orange:hover
      {
      cursor: pointer;
      background: #ff9000;
      }
      
    div.css_bar_graph div.graph li.purple:hover
      {
      cursor: pointer;
      background: #7d47ba;
      }
      
    div.css_bar_graph div.graph li.red:hover
      {
      cursor: pointer;
      background: #d23648;
      }
      
    div.css_bar_graph div.graph li.bar:hover span
      {
      cursor: pointer;
      top: -60px;
      padding: 10px;
      margin: 0px;
      }

    /*Pie charts*/
    /* 
      make each pie piece a rectangle twice as high as it is wide.
      move the transform origin to the middle of the left side.
      Also ensure that overflow is set to hidden.
    */
      .pie {
        position:absolute;
        width:100px;
        height:200px;
        overflow:hidden;
        left:150px;
        -moz-transform-origin:left center;
        -ms-transform-origin:left center;
        -o-transform-origin:left center;
        -webkit-transform-origin:left center;
        transform-origin:left center;
      }
    /*
      unless the piece represents more than 50% of the whole chart.
      then make it a square, and ensure the transform origin is
      back in the center.

      NOTE: since this is only ever a single piece, you could
      move this to a piece specific rule and remove the extra class
    */
      .pie.big {
        width:200px;
        height:200px;
        left:50px;
        -moz-transform-origin:center center;
        -ms-transform-origin:center center;
        -o-transform-origin:center center;
        -webkit-transform-origin:center center;
        transform-origin:center center;
      }
    /*
      this is the actual visible part of the pie. 
      Give it the same dimensions as the regular piece.
      Use border radius make it a half circle.
      move transform origin to the middle of the right side.
      Push it out to the left of the containing box.
    */
      .pie:BEFORE {
        content:"";
        position:absolute;
        width:100px;
        height:200px;
        left:-100px;
        border-radius:100px 0 0 100px;
        -moz-transform-origin:right center;
        -ms-transform-origin:right center;
        -o-transform-origin:right center;
        -webkit-transform-origin:right center;
        transform-origin:right center;
        
      }
     /* if it's part of a big piece, bring it back into the square */
      .pie.big:BEFORE {
        left:0px;
      }
    /* 
      big pieces will also need a second semicircle, pointed in the
      opposite direction to hide the first part behind.
    */
      .pie.big:AFTER {
        content:"";
        position:absolute;
        width:100px;
        height:200px;
        left:100px;
        border-radius:0 100px 100px 0;
      }
    /*
      add colour to each piece.
    */
      .pie:nth-of-type(1):BEFORE,
      .pie:nth-of-type(1):AFTER {
        background-color:blue;  
      }
      .pie:nth-of-type(2):AFTER,
      .pie:nth-of-type(2):BEFORE {
        background-color:green; 
      }
      .pie:nth-of-type(3):AFTER,
      .pie:nth-of-type(3):BEFORE {
        background-color:red; 
      }
      .pie:nth-of-type(4):AFTER,
      .pie:nth-of-type(4):BEFORE {
        background-color:orange;  
      }
    /*
      now rotate each piece based on their cumulative starting
      position
    */
      .pie[data-start="30"] {
        -moz-transform: rotate(30deg); /* Firefox */
        -ms-transform: rotate(30deg); /* IE */
        -webkit-transform: rotate(30deg); /* Safari and Chrome */
        -o-transform: rotate(30deg); /* Opera */
        transform:rotate(30deg);
      }
      .pie[data-start="60"] {
        -moz-transform: rotate(60deg); /* Firefox */
        -ms-transform: rotate(60deg); /* IE */
        -webkit-transform: rotate(60deg); /* Safari and Chrome */
        -o-transform: rotate(60deg); /* Opera */
        transform:rotate(60deg);
      }
      .pie[data-start="100"] {
        -moz-transform: rotate(100deg); /* Firefox */
        -ms-transform: rotate(100deg); /* IE */
        -webkit-transform: rotate(100deg); /* Safari and Chrome */
        -o-transform: rotate(100deg); /* Opera */
        transform:rotate(100deg);
      }
    /*
      and rotate the amount of the pie that's showing.

      NOTE: add an extra degree to all but the final piece, 
      to fill in unsightly gaps.
    */
      .pie[data-value="30"]:BEFORE {
        -moz-transform: rotate(31deg); /* Firefox */
        -ms-transform: rotate(31deg); /* IE */
        -webkit-transform: rotate(31deg); /* Safari and Chrome */
        -o-transform: rotate(31deg); /* Opera */
        transform:rotate(31deg);
      }
      .pie[data-value="40"]:BEFORE {
        -moz-transform: rotate(41deg); /* Firefox */
        -ms-transform: rotate(41deg); /* IE */
        -webkit-transform: rotate(41deg); /* Safari and Chrome */
        -o-transform: rotate(41deg); /* Opera */
        transform:rotate(41deg);
      }
      .pie[data-value="260"]:BEFORE {
        -moz-transform: rotate(260deg); /* Firefox */
        -ms-transform: rotate(260deg); /* IE */
        -webkit-transform: rotate(260deg); /* Safari and Chrome */
        -o-transform: rotate(260deg); /* Opera */
        transform:rotate(260deg);
      }
    /*
    NOTE: you could also apply custom classes (i.e. .s0 .v30)
    but if the CSS3 attr() function proposal ever gets implemented,
    then all the above custom piece rules could be replaced with
    the following:

    .pie[data-start] {
       transform:rotate(attr(data-start,deg,0);
    }
    .pie[data-value]:BEFORE {
       transform:rotate(attr(data-value,deg,0);
    }
    */
  </style>
  <title></title>
</head>
<body>
  <div align="center"><u><h2>Attendance Report for the Month of September 2020</h2></u></div>

  <!-- PJP css bar graph -->
  <div class="css_bar_graph">
    
    <!-- y_axis labels -->
    <ul class="y_axis">
      <li>100%</li><li>80%</li><li>60%</li><li>40%</li><li>20%</li><li>0%</li>
    </ul>
    
    <!-- x_axis labels -->
    <ul class="x_axis">
      <li>% TSI reported between 9.30 to 10.30</li>
      <li>% TSI reported between 10.31 to 11.30</li>
      <li>% TSI reported after 11.30</li>
      <li>% TSI on leave</li>
    </ul>
    
    <!-- graph -->
    <div class="graph">
      <!-- grid -->
      <ul class="grid">
        <li><!-- 100 --></li>
        <li><!-- 80 --></li>
        <li><!-- 60 --></li>
        <li><!-- 40 --></li>
        <li><!-- 20 --></li>
        <li class="bottom"><!-- 0 --></li>
      </ul>
      
      <!-- bars -->
      <!-- 250px = 100% -->
      <ul>
        <li class="bar nr_1 blue" 
          style="height: 140px;"
        ><div class="top"></div><div class="bottom"></div><span>({{ $pcount1 }})%</span></li>
        <li class="bar nr_2 blue" style="height: 30px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount2 }})%</span></li>
        <li class="bar nr_3 blue" style="height: 1px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount3 }})%</span></li>
        <li class="bar nr_4 blue" style="height: 1px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount4 }})% </span></li>

        <!-- <li class="bar nr_1 blue" 
          style="height: 5px;"
        ><div class="top"></div><div class="bottom"></div><span>({{ $pcount1 }})%</span></li>
        <li class="bar nr_2 blue" style="height: 5px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount2 }})%</span></li>
        <li class="bar nr_3 blue" style="height: 5px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount3 }})%</span></li>
        <li class="bar nr_4 blue" style="height: 5px;"><div class="top"></div><div class="bottom"></div><span>({{ $pcount4 }})% </span></li> -->
      </ul> 
    </div>
    
    <!-- graph label -->
    <div class="label"><span>Graph: </span>PJP Adhered Report for the Month of September 2020</div>
    <!-- <div class="label"><span>Graph: </span>PJP Report for the Month of September 2020</div> -->
  </div>

  <br>
  <br>

  <!-- PJP css bar graph -->
  <div class="css_bar_graph">
    
    <!-- y_axis labels -->
    <ul class="y_axis">
      <li>100%</li><li>80%</li><li>60%</li><li>40%</li><li>20%</li><li>0%</li>
    </ul>
    
    <!-- x_axis labels -->
    <ul class="x_axis">
      <li>% of Days worked</li>
      <li>% of days PJP adhered</li>
      <li>% TSI’s GPS was ON</li>
    </ul>
    
    <!-- graph -->
    <div class="graph">
      <!-- grid -->
      <ul class="grid">
        <li><!-- 100 --></li>
        <li><!-- 80 --></li>
        <li><!-- 60 --></li>
        <li><!-- 40 --></li>
        <li><!-- 20 --></li>
        <li class="bottom"><!-- 0 --></li>
      </ul>
      
      <!-- bars -->
      <!-- 250px = 100% -->
      <ul>
        <li class="bar nr_1 blue" style="height: 250px;"><div class="top"></div><div class="bottom"></div><span>100%</span></li>
        <li class="bar nr_2 blue" style="height: 250px;"><div class="top"></div><div class="bottom"></div><span>100%</span></li>
        <li class="bar nr_3 blue" style="height: 250px;"><div class="top"></div><div class="bottom"></div><span>100%</span></li>
      </ul> 
    </div>
    
    <!-- graph label -->
    <div class="label"><span>Graph: </span>Cumulative Report for the Month of September 2020</div>
    <!-- <div class="label"><span>Graph: </span>PJP Report for the Month of September 2020</div> -->
  </div>

  <br>

  <div align="center">
    <table>
      <tr>
        <td class="yellow" rowspan="2">Day</td>
        <td class="yellow" rowspan="2">Date</td>
        <td class="yellow" rowspan="2">Region</td>
        <td class="yellow" rowspan="2">ASM Area</td>
        <td class="yellow" rowspan="2">ASM Name</td>
        <td class="yellow" rowspan="2">SO Name</td>
        <td class="yellow" rowspan="2">HQ</td>
        <td class="yellow" rowspan="2">Associate Name</td>
        <td class="yellow" rowspan="2">Employee ID</td>
        <td class="yellow" rowspan="2">UID No</td>
        <td class="yellow" rowspan="2">Designation</td>
        <td class="yellow" rowspan="2">Day Start Time</td>
        <!-- <td class="yellow" rowspan="2">PJP Adhered Time</td> -->
        <td class="yellow" rowspan="2">Day End Time</td>
        <td class="yellow" rowspan="2">REPORTED BEFORE 10.30AM</td>
        <td class="yellow" rowspan="2">REPORTED BETWEEN 10.31-11.30AM</td>
        <td class="yellow" rowspan="2">AFTER 11.30AM</td>
        <td class="yellow" rowspan="2">ON LEAVE</td>
        <!-- <td class="yellow" colspan="2">T O W N</td>
        <td class="yellow" colspan="2">PJP ADEHERED</td> -->
        <!-- <td class="yellow" rowspan="2">IF NO, REASON</td> -->
        <td class="yellow" colspan="2">GPS</td>
        <td class="yellow" rowspan="2">BATTERY</td>
        <td class="yellow" rowspan="2">Coordinates</td>
        <td class="yellow" rowspan="2">Address</td>
      </tr>
      <tr>
        <!-- <td class="yellow">P L A N</td>
        <td class="yellow">A C T U A L</td>
        <td class="yellow">Yes</td>
        <td class="yellow">No</td> -->
        <td class="yellow">Yes</td>
        <td class="yellow">No</td>
      </tr>
      @for($i = 0; $i < sizeof($data); $i++)
        <hr>
        @for($j = 0; $j < sizeof($data[$i]); $j++)
        <tr style="
          @if(!strcmp($data[$i][$j]['day'], 'Sun')) 
            background-color: lightblue; 
          @elseif(!strcmp($data[$i][$j]['on_leave'], 'YES'))
            background-color: lightcoral; 
          @elseif(!strcmp($data[$i][$j]['actual'], 'Total'))
            background-color: lightgrey; 
          @elseif(!strcmp($data[$i][$j]['actual'], '% PJP Adhered'))
            background-color: lightgrey; 
          @elseif(!strcmp($data[$i][$j]['actual'], 'Total PJP'))
            background-color: lightgrey; 
          @endif">
          <td>{{ $data[$i][$j]['day'] }}</td>
          <td>{{ $data[$i][$j]['date'] }}</td>
          <td>{{ $data[$i][$j]['region'] }}</td>
          <td>{{ $data[$i][$j]['asm_area'] }}</td>
          <td>{{ $data[$i][$j]['asm_name'] }}</td>
          <td>{{ $data[$i][$j]['so_name'] }}</td>
          <td>{{ $data[$i][$j]['hq'] }}</td>
          <td>{{ $data[$i][$j]['associate_name'] }}</td>
          <td>{{ $data[$i][$j]['employee_code'] }}</td>
          <td>{{ $data[$i][$j]['uid_no'] }}</td>
          <td>{{ $data[$i][$j]['designation'] }}</td>
          <td>{{ $data[$i][$j]['start_time'] }}</td>
          <!-- <td> - </td> -->
          <!-- <td>{{ $data[$i][$j]['pjp_time'] }}</td> -->
          <td>{{ $data[$i][$j]['end_time'] }}</td>
          <td>{{ $data[$i][$j]['before_10_30'] }}</td>
          <td>{{ $data[$i][$j]['between_10_30_11_30'] }}</td>
          <td>{{ $data[$i][$j]['after_11_30'] }}</td>
          <td>{{ $data[$i][$j]['on_leave'] }}</td>
          <!-- <td>{{ $data[$i][$j]['plan'] }}</td> -->
          <!-- <td>{{ $data[$i][$j]['plan'] == "" && $data[$i][$j]['start_time'] != "" ? 'baburhat' : $data[$i][$j]['plan'] }}</td> -->
          <!-- <td>{{ $data[$i][$j]['actual'] }}</td> -->
          <!-- <td>{{ $data[$i][$j]['actual'] == "" && $data[$i][$j]['start_time'] != "" ? 'Baburhat, Mumbai' : $data[$i][$j]['actual'] }}</td> -->
          <!-- <td>{{ $data[$i][$j]['pjp_adhered'] }}</td>
          <td>{{ $data[$i][$j]['pjp_not_adhered'] }}</td>
          <td>{{ $data[$i][$j]['pjp_not_adhered'] == 'NO' ? ($data[$i][$j]['actual'] == 'Total' || $data[$i][$j]['actual'] == 'Total PJP' || $data[$i][$j]['actual'] == '% PJP Adhered' ? '' : 'On Leave') : '' }}</td> -->
          <td>{{ $data[$i][$j]['gps'] }}</td>
          <td></td>
          <td>{{ $data[$i][$j]['battery'] }}</td>
          <td>{{ $data[$i][$j]['coordinates'] }}</td>
          <td>{{ $data[$i][$j]['address'] }}</td>
        </tr>
        @endfor
      @endfor
    </table>  
  </div>
</body>
</html>