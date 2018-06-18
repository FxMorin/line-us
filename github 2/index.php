<?php
require_once('includes/config.php');
security('auth/login.php');
?>
<html>

<head>
    <title>Line-us Remade</title>

    <link href="css/layout.css" rel="stylesheet" />
    <link href="css/slider.css" rel="stylesheet" />
    <link href="css/controls.css" rel="stylesheet" />
    <link href="css/simplepaint.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css?family=Baloo" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <nav>
        <h1></h1>
    </nav>
    <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    <button class="logout" style="left:79%;" onclick="window.location.href='dashboard.php'">Dashboard</button>

    <div class="output" id="output">
        <span id="close-output" class="close">X</span>
        <textarea class="output-content" id="output-content"></textarea>
    </div>

    <div class="content">
        <div class="pane" id="production-line">
            <div class="slider" id="production-slider" style="">

                <!-- SLIDE #1 -->
                <section class="slide" data-onleave="validateSize">
                    <h2>Start App</h2>

                    <div class="controls">
                        <label>
                             <span style="visibility:hidden" class="badge">1</span>
                             <span style="visibility:hidden" >Width (mm)</span>
                        </label>
                        <input style="visibility:hidden" type="number" value="55" min="0" id="print-width" />
                        <br/>
                        <br/>
                        <label>
                             <span style="visibility:hidden" class="badge">2</span>
                             <span style="visibility:hidden" >Height (mm)</span>
                        </label>
                        <input style="visibility:hidden" type="number" value="100" min="0" id="print-height" />
                    </div>

                    <button style="width:400px;height:160px;font-size:62px;" id="money" class="slide-next">Start</button>
                </section>

                <!-- SLIDE #2 -->
                <section class="slide" data-onleave="leavingCanvas">
                    <h2>Sketch</h2>
                    <div id="sketch-canvas" class="sketch-canvas"></div>

                    <label for="image-upload" class="upload-label">Upload an image!</label>
                    <input type="file" id="image-upload">

                    <button class="slide-prev">Prev</button>
                    <button class="slide-next">Next</button>
                </section>

                <!-- SLIDE #3 -->
                <section class="slide">
                    <h2 style="visibility:hidden" >Lettering</h2>

                    <div class="controls">
                        <label>
                             <span style="visibility:hidden" class="badge">3</span>
                             <span style="visibility:hidden" >Top Text</span>
                        </label>
                        <input style="visibility:hidden" type="text" placeholder="The text that will appear on top of the image" id="top-text" />
                        <br/>
                        <br/>
                        <label>
                             <span style="visibility:hidden" class="badge">4</span>
                             <span style="visibility:hidden" >Bottom Text</span>
                        </label>
                        <input style="visibility:hidden" type="text" placeholder="The text that will appear under the image" id="bottom-text" />
                    </div>

                    <button style="visibility:hidden" class="slide-prev">Prev</button>
                    <button style="visibility:hidden" class="slide-next">Next</button>
                </section>

                <!-- SLIDE #4 -->
                <section class="slide" data-onleave="setOptions">
                    <h2>Conver to GCode</h2>

                    <div class="controls">
                        <label>
                             <span style="visibility:hidden" class="badge">5</span>
                             <span style="visibility:hidden" >Drill bit width (mm)</span>
                        </label>
                        <input style="visibility:hidden" type="number" id="bit-width" value="2" />
                        <br/>
                        <br/>
                        <label>
                             <span style="visibility:hidden" class="badge">6</span>
                             <span style="visibility:hidden" >Cut Depth (mm per layer)</span>
                        </label>
                        <input style="visibility:hidden" type="number" id="cut-depth" value="2" />
                        <br/>
                        <br/>
                        <label>
                             <span style="visibility:hidden" class="badge">7</span>
                             <span style="visibility:hidden" >Number of Layers</span>
                        </label>
                        <input style="visibility:hidden" type="number" id="number-cuts" value="3" />
                    </div>

                    <button style="visibility:hidden" class="slide-prev">Prev</button>
                    <button id="money2" style="width:400px;height:160px;font-size:62px;" class="slide-next">Finish!</button>
                    <label style="visibility:hidden" for="isCompressed">Compression Rate:</label>
                    <input style="visibility:hidden" id="isCompressed" placeholder="1-10" value="1" style="width:70px;height:22px" type="text"></input>
                </section>
            </div>
        </div>
        <div class="pane" id="preview">
            <h2 class="invert">Preview</h2>

            <canvas id="preview-canvas" class="preview-canvas" width="0" height="0"></canvas>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            initSlider();
        }, false)
    </script>
    <div id="coolCode" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></div>
    <div id="betterCode" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></div>
    <svg id="svgHere2" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></svg>
    <svg id="svgHere" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></svg>
    <div id="crick" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></div>
    <div id="crick2" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></div>
    <div id="username" type="hidden" style="visibility:false;display:none;"><?php echo $_SESSION['Username']; ?></div>
    <canvas style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;" id="theTrueCanvas"></canvas>
    <div id="bestCode" style="width:2500px;height:2500px;border-style:solid;border-color:black;border-size:2px;position:fixed;left:-99999px;visibility:false;"></div>

    <script src="libraries/floodFill.js" type="text/javascript"></script>
    <script src="js/GCodeArray.js" type="text/javascript"></script>
    <script src="libraries/jquery-3.1.1.min.js" type="text/javascript"></script>
    <script src="libraries/easeljs-0.8.2.min.js" type="text/javascript"></script>
    <script src="libraries/simplepaint.js" type="text/javascript"></script>
    <script src="js/gcode.js" type="text/javascript"></script>
    <script src="libraries/imageTracer.js" type="text/javascript"></script>
    <script src="js/bezier.js" type="text/javascript"></script>
    <script src="libraries/svg.min.js"></script>
    <script src="js/svg-gcode.js" type="text/javascript"></script>
    <script src="js/slider.js" type="text/javascript"></script>

</body>

</html>
