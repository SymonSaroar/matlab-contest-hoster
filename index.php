<!DOCTYPE html>
<html>

    <head>
        <title>Standings | MATLAB MANIA</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="src/script.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>

    <body>

        <!-- Top bar with background -->
        <div class="main">
            <nav class="container">
                <ul>
                    <li>HORIZON | A Vision To Support Lives</li>
                </ul>
            </nav>
                <img src="./images/club.png" />
        </div>


        <!-- Comments -->
        <div class="container supporting">
            <img class="rotate" src="./images/beans.png" />
            <div class="description">
                <h2>“Before software can be reusable it first has to be usable.” – Ralph Johnson</h2>
                <p>
                    Let's solve problems that influence you to think creatively, help you to craft something distinct, let's have some fun to decode the puzzles.
                </p>
                <p># LET'S_CRACK_SOME_CODE</p>
            </div>
        </div>


        <!-- Standing Tag -->
        <div class="rating">
            <div class="container" id="standings">
                <h1># STANDINGS</h1>
                <p>~ MATLAB MANIA</p>
            </div>
        </div>
        <!-- Clock Countdown -->
        <div id="countdown">
            <h1 id="total_time"><span id="hour">4</span> HOUR : <span id="min">30</span> MIN</h1>
        </div>


        <!-- Button of hidden layer -->
        <!-- <button onclick="myFunction()"><img src="./images/infinity.png" alt=""></button>
        <div id="hidden" class="list">
            
        </div> -->
        <center>
            <div id="table_status" style="width: 40%; padding-bottom: 20px">
                <?php
                    include "livest.php";
                ?>
            </div>
            <div id="table_div" class="table_div">
                <?php
                    include "read.php";
                ?>
            </div>
        </center>





        <!-- Auto Play Music 
        <audio loop autoplay> 
            <source src="./images/2.mp3">
            <source src="./images/1.wav">
        </audio>
-->

        <!-- Footer Part -->
        <div class="container lastpart">
            <h1>A Fundraising Event To Support Lives</h1>
        </div>

        <footer>
            <div class="container">
                <div class="copyright">
                    &copy; <b>HORIZON |</b> A Vision To Support Lives
                </div>

                <nav>
                    <ul>
                        <li>KUET Career Club</li>
                        <li>EEE Makers HUB</li>
                        <li>CADers</li>
                    </ul>
                </nav>
            </div>
        </footer>
        <!-- JS Part-->
        <script>

            // Countdown
            var last_hrs = 23;
            var last_min = 20;
            let m_diff = -1, h_diff = -1;
            setInterval(() => {
                m_diff = 0;     // Comment for starting Countdown 
                h_diff = 0;     // Comment for starting Countdown
                if (m_diff === 0 && h_diff === 0) {
                    document.getElementById("total_time").innerHTML = 
                    'PRACTICE';
                }
                min--;
                var d = new Date();
                var min = d.getMinutes();
                var hrs = d.getHours();
                

                var date1 = new Date(2020, 10, 8,  hrs, min);
                var date2 = new Date(2020, 10, 8, last_hrs, last_min);
                if (date2 < date1) {
                    date2.setDate(date2.getDate() + 1);
                }
                var diff = date2 - date1;
                // 28800000 milliseconds (8 hours)

                var msec = diff;
                h_diff = Math.floor(msec / 1000 / 60 / 60);
                msec -= h_diff * 1000 * 60 * 60;
                m_diff = Math.floor(msec / 1000 / 60);

                // console.log(h_diff);
                // console.log(m_diff);
                if (m_diff >= 0) {
                    hourid = document.getElementById("hour");
                    if(hourid != null) hourid.innerHTML = h_diff;
                    minid = document.getElementById("min");
                    if(minid != null) minid.innerHTML = m_diff;
                }

            }, 100);


            //JS for Button | HIDDEN LAYER
            function myFunction() {
                var x = document.getElementById("hidden");
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            }
        </script>
    </body>

</html>
