var slabScale = 10;
var bitWidth = 2;
var newline = "\n";
var svg1 = "";
var lastL = "";
var ControlPointX = 0;
var ControlPointY = 0;
var theX = 0;
var theY = 0;
var stillSVG = "";
var tmpWord;
var first = false;
var offset = Math.round((bitWidth * slabScale) / 2);

function svgGcode(thePaths) {
	svg1 = "";
	for (var i = 0; i < thePaths.length; i++) {
		switch(thePaths[i][0]) {
    	case "M" || "m":
        svg1 += svgM1(thePaths, i);
        break;
    	case "L" || "l":
        svg1 += svgL1(thePaths, i);
        break;
			case "H" || "h":
	      svg1 += svgH1(thePaths, i);
	      break;
			case "V" || "v":
				svg1 += svgV1(thePaths, i);
				break;
			case "C" || "c":
				svg1 += svgC1(thePaths, i);
				break;
			case "S" || "s":
				svg1 += svgS1(thePaths, i);
				break;
			case "Q" || "q":
				svg1 += svgQ1(thePaths, i);
				break;
			case "T" || "t":
				svg1 += svgT1(thePaths, i);
				break;
			case "A" || "a":
				svg1 += svgA1(thePaths, i);
				break;
			case "Z" || "z":
				svg1 += svgZ1(thePaths, i);
				break;
    	default:
        break;
		}
	}
	first = false;
	return svg1;
}
function PointXY(xx, yy) {
    this.theX = xx;
    this.theY = yy;
}
function Point(xx, yy) {
    this.x = xx;
    this.y = yy;
}
function svgM1(thePaths, i) {
	tmpWord = fastMove(thePaths[i][1],thePaths[i][2]);
	theX = thePaths[i][1];
	theY = thePaths[i][2];
	lastL = "M";
	return tmpWord;
}
function svgL1(thePaths, i) {
	theX = thePaths[i][1];
	theY = thePaths[i][2];
	lastL = "L";
	return slowMove(thePaths[i][1],thePaths[i][2]);
}
function svgH1(thePaths, i) {
	theX = thePaths[i][1];
	lastL = "H";
	return slowMove(thePaths[i][1],theY);
}
function svgV1(thePaths, i) {
	theY = thePaths[i][1];
	lastL = "V";
	return slowMove(theX, thePaths[i][1]);
}
function svgC1(thePaths, i) {
	var point1 = new PointXY(theX,theY);
	var handle1 = new Point(thePaths[i][1],thePaths[i][2]);
	var handle2 = new Point(thePaths[i][3],thePaths[i][4]);
	var point2 = new Point(thePaths[i][5],thePaths[i][6]);
	var bezierLength = Bezier(point1, handle1, handle2,point2);
	//var bezierAmount = 10;
	var bezierAmount = bezierLength / (bezierLength / 20);
	var bezierPoint = bezierLength / bezierAmount;
	var points = [];
	var tmpWord = "";
	var q;
	for (q = 0; q <= bezierAmount; q++) {
		var bezierP = BezierP(point1, handle1, handle2,point2,bezierPoint*q);
		tmpWord += slowMove(bezierP.theX, bezierP.theY);
	}
	lastL = "C";
	theX = thePaths[i][5];
	theY = thePaths[i][6];
	return tmpWord;
}
function svgS1(thePaths, i) {
	if (thePaths[i][1] > theX) {
		if (thePaths[i][2] > theY) {
			tmpWord = slowMove(thePaths[i][1], theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3]-(theX+(thePaths[i][1]-theX)), theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3], thePaths[i][4]);
		} else {
			tmpWord = slowMove(thePaths[i][1], thePaths[i][2]+(theY-thePaths[i][2])/2);
			tmpWord += slowMove(thePaths[i][3]-(theX+(thePaths[i][1]-theX)), theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3], thePaths[i][4]);
		}
	} else {
		if (thePaths[i][2] > theY) {
			tmpWord = slowMove(thePaths[i][1], theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3]-(thePaths[i][1]+(theX-thePaths[i][1])), theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3], thePaths[i][4]);
		} else {
			tmpWord = slowMove(thePaths[i][1], thePaths[i][2]+(theY-thePaths[i][2])/2);
			tmpWord += slowMove(thePaths[i][3]-(theX-thePaths[i][1]), theY+(thePaths[i][2]-theY)/2);
			tmpWord += slowMove(thePaths[i][3], thePaths[i][4]);
		}
	}
	lastL = "S";
	theX = thePaths[i][3];
	theY = thePaths[i][4];
	return tmpWord;
}
function svgQ1(thePaths, i) {
	var point1 = new PointXY(theX,theY);
	var handle1 = new Point(thePaths[i][1],thePaths[i][2]);
	var handle2 = new Point(thePaths[i][3],thePaths[i][4]);
	var bezierLength = Quadratic(point1, handle1, handle2);
	//var bezierAmount = 10;
	var bezierAmount = bezierLength / (bezierLength / 20);
	var bezierPoint = bezierLength / bezierAmount;
	var points = [];
	var tmpWord = "";
	var k;
	for (k = 1; k <= bezierAmount; k++) {
		var bezierP = QuadraticP(point1, handle1, handle2,bezierPoint*k);
		tmpWord += slowMove(bezierP.x, bezierP.y);
	}
	lastL = "Q";
	theX = thePaths[i][3];
	theY = thePaths[i][4];
	return tmpWord;
}
function svgT1(thePaths, i) {
	if (lastL == "Q") {
		if (ControlPointY > theY) {
			tmpWord = slowMove(ControlPointX, theY-(ControlPointY-theY));
			tmpWord += slowMove(thePaths[i][1], thePaths[i][2]);
		} else {
			tmpWord = slowMove(ControlPointX, ControlPointY-(theY-ControlPointY));
			tmpWord += slowMove(thePaths[i][1], thePaths[i][2]);
		}
	} else {
		tmpWord = slowMove(thePaths[i][1], thePaths[i][2]);
	}
	lastL = "T";
	theX = thePaths[i][1];
	theY = thePaths[i][2];
	return tmpWord;
}
function svgA1(thePaths, i) {
	lastL = "A";
	theX = thePaths[i][6];
	theY = thePaths[i][7];
	return slowMove(thePaths[i][6], thePaths[i][7]);  //W.I.P.
}
function svgZ1(thePaths, i) {
	lastL = "Z";
	return raiseBit();
}

function check1(thePaths, i, num){
	if (thePaths[i].length < num) {
		return true;
	} else {
		console.log("too long");
		return false;
	}
}

function raiseBit() {
	stillSVG += " Z ";
	return "G01 " + move(theX, theY) + " Z1000" + newline;
}
function fastMove(theX, theY) {
	if (first == false) {
		stillSVG += "M " + theX + " " + theY;
		first = true;
	} else {
		stillSVG += " M " + theX + " " + theY;
	}
	return "G01 " + move(theX, theY) + " Z1000" + newline;
}
function slowMove(theX, theY) {
	stillSVG += " L " + Math.round(theX,2) + " " + Math.round(theY,2);
	return "G01 " + move(theX, theY) + " Z0" + newline;
}
function move(theX, theY) {
	return "X" + Math.round((((theX / slabScale)*100)/5)+650) + " Y" + Math.round((((theY / slabScale)*100)/5)-1000);
}
