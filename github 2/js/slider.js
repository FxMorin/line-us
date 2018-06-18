var _previewCanvas;
var _topText;
var _bottomText;
var paneWidth;
var canvasManager;
var currentImage;
var imgd2;
var theSvg;

var SLAB_SCALE = 10;
var SELECTED_WIDTH = -1;
var SELECTED_HEIGHT = -1;

function initSlider() {
    _previewCanvas = document.getElementById("preview-canvas");
    _topText = document.getElementById("top-text");
    _bottomText = document.getElementById("bottom-text");

    var _productionLine = document.getElementById("production-line");
    var _productionSlider = document.getElementById("production-slider");
    document.getElementById("money").style.width = _productionSlider.clientWidth - 40;
    document.getElementById("money").style.marginBottom = _productionSlider.clientHeight / 2 - 75;
    document.getElementById("money2").style.width = _productionSlider.clientWidth - 40;
    document.getElementById("money2").style.marginBottom = _productionSlider.clientHeight / 2 - 75;
    var _slides = document.getElementsByClassName("slide");
    var _slideNextButtons = document.getElementsByClassName("slide-next");
    var _slidePrevButtons = document.getElementsByClassName("slide-prev");

    var slideIndex = 0;
    paneWidth = _productionLine.clientWidth;

    // set slides to width of _productionLine
    for (var i = 0; i < _slides.length; i++) {
        _slides[i].style.width = "" + paneWidth + "px";
    }

    // set production slider width to (_productionLine width * count of _slides)
    _productionSlider.style.width = "" + (paneWidth * _slides.length) + "px";

    // add click event for slider next buttons
    for (var i = 0; i < _slideNextButtons.length; i++) {
        _slideNextButtons[i].addEventListener("click", function (e) {
            var allGood = true;
            var _button = e.currentTarget;
            var _slide = _button.closest(".slide");
            var func = window[_slide.dataset.onleave];

            if (func && typeof func == "function") {
                allGood = func();
            }

            if (allGood) {
                if (slideIndex < _slides.length - 1) {
                    slideIndex++;
                    if (slideIndex == 2) {
                      slideIndex = 3;
                    }
                    _productionSlider.style.transform = "translateX(" + (slideIndex * paneWidth) * -1 + "px)";
                } else {
                    addingSVG();
                }
            }
        });
    }

    // add click event for slider prev buttons
    for (var i = 0; i < _slidePrevButtons.length; i++) {
        _slidePrevButtons[i].addEventListener("click", function (e) {
            if (slideIndex > 0) {
                slideIndex--;
                _productionSlider.style.transform = "translateX(" + (slideIndex * paneWidth) * -1 + "px)";
            }
        });
    }

    // add event listener for top & bottom text inputs
    _topText.onkeyup = function () {
        redrawCanvas(_topText.value, _bottomText.value);
    };

    _bottomText.onkeyup = function () {
        redrawCanvas(_topText.value, _bottomText.value);
    };

    // close event on output
    document.getElementById("close-output").addEventListener("click", function () {
        document.getElementById("output").classList.remove("show");
    });


    var _fileUploader = document.getElementById("image-upload");
    _fileUploader.onchange = function () {
        var file = _fileUploader.files[0];

        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            currentImage = reader.result;
            redrawCanvas("","");
        };

    };
}

function validateSize() {
    var _printWidth = document.getElementById("print-width");
    var _printHeight = document.getElementById("print-height");

    var printWidth = parseInt(_printWidth.value);
    var printHeight = parseInt(_printHeight.value);

    if (printWidth > 0 && printHeight > 0) {
        SELECTED_WIDTH = printWidth;
        SELECTED_HEIGHT = printHeight;

        setCanvasSize();
        return true;
    }

    alert("Values must be above 0");

    return false;
}

function setCanvasSize() {
    var _canvas = document.getElementById("sketch-canvas");

    // empty the canvas div
    while (_canvas.firstChild) {
        _canvas.removeChild(_canvas.firstChild);
    }

    var ratio = SELECTED_HEIGHT / SELECTED_WIDTH;

    var canvasWidth = paneWidth;
    var canvasHeight = canvasWidth * ratio;

    _canvas.width = canvasWidth;
    _canvas.height = canvasHeight;

    _previewCanvas.width = canvasWidth-40;
    _previewCanvas.height = canvasHeight-40;

    var $canvas = $(_canvas);
    var options = {
        colours: ["#000000"],
        brushSizes: [10],
        height: canvasHeight
    };

    currentImage = null;
    canvasManager = new simplepaint.CanvasManager($canvas, options);
}

function leavingCanvas() {
    if (!currentImage) {
        currentImage = canvasManager.getImage();
    }
    redrawCanvas(_topText.value, _bottomText.value);

    return true;
}

function redrawCanvas(topText, bottomText) {
    // clear the canvas
    var context = _previewCanvas.getContext("2d");
    var canvas2 = document.getElementById("hacking");
    var context2 = canvas2.getContext("2d");
    context.clearRect(0, 0, _previewCanvas.width, _previewCanvas.height);
    context.fillStyle = "#FFFFFF";
    context.fillRect(0, 0, _previewCanvas.width, _previewCanvas.height);

    // add the image
    var sketchImage = new Image();
    sketchImage.src = currentImage;
    sketchImage.onload = function () {
        context.drawImage(sketchImage, 0, 1, sketchImage.width, sketchImage.height);
    }
    var sketchImage2 = new Image();
    sketchImage2.src = currentImage;
    sketchImage2.onload = function () {
        context2.drawImage(sketchImage, 0, 1, sketchImage.width, sketchImage.height);
    }
}

function setOptions() {
    var _bitWidth = document.getElementById("bit-width");
    var _cutDepth = document.getElementById("cut-depth");
    var _numberCuts = document.getElementById("number-cuts");

    bitWidth = 10;
    cutDepth = 1;
    maxCutDepth = 1;

    return true;
}

function addingSVG(){
  var options = {ltres:16, qtres:16, pathomit:24, rightangleenhance:true, colorsampling:0, numberofcolors:2, mincolorratio:0, colorquantcycles:1, blurradius:0, blurdelta:20, strokewidth:1, linefilter:true, scale:1, roundcoords:3, lcpr:0, qcpr:0, desc:false, viewbox:false, corsenabled:false};
  imgd2 = ImageTracer.getImgdata( _previewCanvas );
  var svgstr2 = ImageTracer.imagedataToSVG( imgd2, options );
  var context = _previewCanvas.getContext("2d");
  context.save();
  context.translate(_previewCanvas.width, 0);
  context.scale(-1, 1);
  context.drawImage(_previewCanvas, 0, 0, _previewCanvas.width, _previewCanvas.height, 0, 0, _previewCanvas.width, _previewCanvas.height);
  context.restore();
	var imgd = ImageTracer.getImgdata( _previewCanvas );
	var svgstr = ImageTracer.imagedataToSVG( imgd, options );
  theSvg = SVG("svgHere").attr({id:"draw",style:"visibility:hidden"});
  thisChain(svgstr, svgstr2);
}

function addingScale(scaler, realPath3){
  var count = 0;
  for (var i = 0; i < realPath3.length; i++) {
    for (var x = 0; x < realPath3.length-i; x++) {
      q = x+i;
      if (realPath3[i] != realPath3[q]) {
        count++;
      }
    }
    if (count == realPath3.length-i-1) {
      newPath3 += realPath3[i] + "\n";
    }
    count = 0;
  }
}

function thisChain(svgstr, svgstr2) {
  var context = _previewCanvas.getContext("2d");
  var yayMore = new Image();
  yayMore.src = "data:image/svg+xml," + encodeURIComponent(svgstr2);
  yayMore.onload = function () {
    context.drawImage(yayMore, 0, 0, _previewCanvas.width, _previewCanvas.height);
  }
  document.getElementById("coolCode").innerHTML = svgstr;
  var ImNearlyCool = document.getElementById("coolCode").querySelectorAll("svg");
  var ImNearCool = ImNearlyCool[0];
  var ImCool = ImNearCool.querySelectorAll("path");
  var path = "";
  var path2 = "";
  var path3 = "";
  var draw = [];
  document.getElementById("betterCode").innerHTML = svgstr;
  var almostBetter = document.getElementById("betterCode").querySelectorAll("svg");
  almostBetter[0].innerHTML = "";
  for (var i = 0; i < ImCool.length; i++) {
    if (ImCool[i].attributes.fill.value != "rgb(255,255,255)") {
      draw[i] = theSvg.path(ImCool[i].attributes.d.value).array();
      path2 = svgGcode2(almostBetter, draw[i].value);
      var anotherSVG = document.createElement("svg");
    	anotherSVG.id = "AGAIN";
    	document.getElementById("crick2").appendChild(anotherSVG);
    	var theSvg3 = SVG(anotherSVG.id).attr({id:"draw3",style:"visibility:hidden"});
    	path2 = theSvg3.path(path2).array();
      path3 += svgGcode(path2.value);
    }
  }
  _previewCanvas.innerHTML = document.getElementById("betterCode").innerHTML;
  var realPath3 = path3.split("\n");
  var newPath3 = "";
  var count = 0;
  for (var i = 0; i < realPath3.length; i++) {
    for (var x = 0; x < realPath3.length-i; x++) {
      q = x+i;
      if (realPath3[i] != realPath3[q]) {
        count++;
      }
    }
    if (count == realPath3.length-i-1) {
      newPath3 += realPath3[i] + "\n";
    }
    count = 0;
  }
  var GCodeArray = GCodeVars(newPath3);
  console.log("highX: " + GCodeArray[0] + "  lowX: " + GCodeArray[1] + "  highY: " + GCodeArray[2] + "  lowY: " + GCodeArray[3])
  console.log("maxX: 1750  lowestX: 650  maxY: 1000  lowestY: -1000")
  if (GCodeArray[0] <= 1750 && GCodeArray[1] >= 650 && GCodeArray[2] <= 1000 && GCodeArray[3] >= -1000) {
    phpDraw(newPath3);
  } else {
    console.log("Image created is out of drawing area!!!");
    var currentScale = 1;
    if (GCodeArray[0] > 1750) {
      currentScale -= (GCodeArray[0]-1750)/1100
    }
    if (GCodeArray[1] < 650) {
      currentScale -= (650-GCodeArray[1])/1100
    }
    if (GCodeArray[2] > 1000) {
      currentScale -= (GCodeArray[2]-1000)/2000
    }
    if (GCodeArray[3] < -1000) {
      currentScale -= (1000-Math.abs(GCodeArray[3]))/2000
    }
    var finalGCODE = GCodeRatio(newPath3, currentScale);
    var ultimateFit = GCodeSizes(finalGCODE);
    console.log("Image sucessfully resized perfectly!!");
    phpDraw(ultimateFit);
  }
}

function phpDraw(gcode) {
    /*if (gcode.length > 20) { ;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xmlhttp.open("POST", "draw.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("gcode="+gcode);
    }*/
    if (gcode.length > 20) { ;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                theBigBear();
            }
        };
        xmlhttp.open("POST", "includes/queue.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("username="+document.getElementById('username').innerHTML+"&gcode="+gcode);
    }
}
function theBigBear() {
  // Poke the big bear
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("POST", "includes/draw.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send();
  window.location.href = "http://192.168.0.245/dashboard.php";
}
