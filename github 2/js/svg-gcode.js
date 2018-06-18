var slabScale = 10;
var bitWidth = 2;
var newline = "\n";
var svg = "";
var lastL = "";
var ControlPointX = 0;
var ControlPointY = 0;
var coolX = 0;
var coolY = 0;
var tmpWord;
var first = false;
var offset = Math.round((bitWidth * slabScale) / 2);

function svgGcode2(almostBetter, thePaths) {
	var canvas = document.getElementById("theTrueCanvas");
	var ctx=canvas.getContext("2d");
	ctx.beginPath();
	ctx.strokeStyle="black";
	displayClassySVG = document.getElementById("bestCode");
  canvas.height = 2500;
  canvas.width = 2500;
  displayClassySVG.height = 2500;
  displayClassySVG.width = 2500;
	for (var i = 0; i < thePaths.length; i++) {
		switch(thePaths[i][0]) {
    	case "M" || "m":
        svg += svgM(thePaths, i, ctx);
        break;
    	case "L" || "l":
        svg += svgL(thePaths, i, ctx);
        break;
			case "H" || "h":
	      svg += svgH(thePaths, i, ctx);
	      break;
			case "V" || "v":
				svg += svgV(thePaths, i, ctx);
				break;
			case "C" || "c":
				svg += svgC(thePaths, i, ctx);
				break;
			case "S" || "s":
				svg += svgS(thePaths, i, ctx);
				break;
			case "Q" || "q":
				svg += svgQ(thePaths, i, ctx);
				break;
			case "T" || "t":
				svg += svgT(thePaths, i, ctx);
				break;
			case "A" || "a":
				svg += svgA(thePaths, i, ctx);
				break;
			case "Z" || "z":
				svg += svgZ(thePaths, i, ctx);
				break;
    	default:
        break;
		}
		/*if (0 == i % 2) {
			var newPath = document.createElementNS("http://www.w3.org/2000/svg", "path");
			newPath.setAttribute('d', svg);
			svg = "";
			var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
			newPath.setAttribute('stroke', hue);
			newPath.setAttribute('fill', "transparent");
			almostBetter[0].appendChild(newPath);
			raiseBit2();
			svg += fastMove2(coolX,coolY);
		}*/
	}
	first = false;
	var yy = 0;
	var xx = 0;
	var startX = [];
  var startY = [];
  var endX = [];
  var endY = [];
	var wasIn = false;
	var firstHit = true;
	var count = 0;
	for (yy = -50; yy <= canvas.height+50; yy = yy + 20) {
		for (f = 0; f <= canvas.height; f++) {
			if (ctx.isPointInPath(f,Math.floor(f/20)+yy, "evenodd")) {
				if (firstHit) {
					startX[count] = f;
					startY[count] = Math.floor(f/20)+yy;
					firstHit = false;
					wasIn = true;
				}
				//ctx.fillRect(f, Math.floor(f/10)+yy,1,1);
			} else {
				if (wasIn) {
					endX[count] = f;
					endY[count] = Math.floor(f/20)+yy;
					ctx.moveTo(startX[count],startY[count]);
					ctx.lineTo(endX[count],endY[count]);
					count++
					wasIn = false;
					firstHit = true;
				}
			}
		}
	}
	/*for (yy = 0; yy <= canvas.height; yy += 10) {
		for (xx = 0; xx <= canvas.width; xx++) {
			slowCircle(ctx, yy+xx/4, xx)
		}
	}*/
	var yello = document.createElementNS("http://www.w3.org/2000/svg", "path");
  var pencil = "";
  for (f = 0; f < count; f++) {
		//if (startX[f] == null || startY[f] == null) {break;}
		if (f == 0) {
    	pencil += "M " + startX[f] + " " + startY[f] + " L " + startX[f] + " " + startY[f] + " L " + endX[f] + " " + endY[f];
		} else {
			pencil += " M " + startX[f] + " " + startY[f] + " L " + startX[f] + " " + startY[f] + " L " + endX[f] + " " + endY[f];
		}
  }
	if (count != 0) {
		yello.setAttribute('fill', 'rgb(0,0,0)');
		yello.setAttribute('stroke', 'rgb(0,0,0)');
		pencil = pencil + " Z";
  	yello.setAttribute('d', pencil);
		document.getElementById("SvgjsSvg1001").style = "";
		document.getElementById("SvgjsSvg1001").style.width = 500;
		document.getElementById("SvgjsSvg1001").style.height = 500;
		document.getElementById("SvgjsSvg1001").appendChild(yello);
		document.getElementById("bestCode").appendChild(document.getElementById("SvgjsSvg1001"));
		svg = svg + pencil
	}
	ctx.stroke();
	return svg;
}
function PointXY(xx, yy) {
    this.coolX = xx;
    this.coolY = yy;
}
function Point(xx, yy) {
    this.x = xx;
    this.y = yy;
}
function svgM(thePaths, i, ctx) {
	coolX = thePaths[i][1];
	coolY = thePaths[i][2];
	lastL = "M";
	return fastMove2(thePaths[i][1],thePaths[i][2], ctx);
}
function svgL(thePaths, i, ctx) {
	coolX = thePaths[i][1];
	coolY = thePaths[i][2];
	lastL = "L";
	return slowMove2(thePaths[i][1],thePaths[i][2], ctx);
}
function svgH(thePaths, i, ctx) {
	coolX = thePaths[i][1];
	lastL = "H";
	return slowMove2(thePaths[i][1],coolY, ctx);
}
function svgV(thePaths, i, ctx) {
	coolY = thePaths[i][1];
	lastL = "V";
	return slowMove2(coolX, thePaths[i][1], ctx);
}
function svgC(thePaths, i, ctx) {
	var point1 = new PointXY(coolX,coolY);
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
		tmpWord += slowMove2(bezierP.coolX, bezierP.coolY, ctx);
	}
	lastL = "C";
	coolX = thePaths[i][5];
	coolY = thePaths[i][6];
	return tmpWord;
}
function svgS(thePaths, i, ctx) {
	if (thePaths[i][1] > coolX) {
		if (thePaths[i][2] > coolY) {
			tmpWord = slowMove2(thePaths[i][1], coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3]-(coolX+(thePaths[i][1]-coolX)), coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3], thePaths[i][4], ctx);
		} else {
			tmpWord = slowMove2(thePaths[i][1], thePaths[i][2]+(coolY-thePaths[i][2])/2, ctx);
			tmpWord += slowMove2(thePaths[i][3]-(coolX+(thePaths[i][1]-coolX)), coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3], thePaths[i][4], ctx);
		}
	} else {
		if (thePaths[i][2] > coolY) {
			tmpWord = slowMove2(thePaths[i][1], coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3]-(thePaths[i][1]+(coolX-thePaths[i][1])), coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3], thePaths[i][4], ctx);
		} else {
			tmpWord = slowMove2(thePaths[i][1], thePaths[i][2]+(coolY-thePaths[i][2])/2, ctx);
			tmpWord += slowMove2(thePaths[i][3]-(coolX-thePaths[i][1]), coolY+(thePaths[i][2]-coolY)/2, ctx);
			tmpWord += slowMove2(thePaths[i][3], thePaths[i][4], ctx);
		}
	}
	lastL = "S";
	coolX = thePaths[i][3];
	coolY = thePaths[i][4];
	return tmpWord;
}
function svgQ(thePaths, i, ctx) {
	var point1 = new PointXY(coolX,coolY);
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
		tmpWord += slowMove2(bezierP.x, bezierP.y, ctx);
	}
	lastL = "Q";
	coolX = thePaths[i][3];
	coolY = thePaths[i][4];
	return tmpWord;
}
function svgT(thePaths, i, ctx) {
	if (lastL == "Q") {
		if (ControlPointY > coolY) {
			tmpWord = slowMove2(ControlPointX, coolY-(ControlPointY-coolY), ctx);
			tmpWord += slowMove2(thePaths[i][1], thePaths[i][2], ctx);
		} else {
			tmpWord = slowMove2(ControlPointX, ControlPointY-(coolY-ControlPointY), ctx);
			tmpWord += slowMove2(thePaths[i][1], thePaths[i][2], ctx);
		}
	} else {
		tmpWord = slowMove2(thePaths[i][1], thePaths[i][2], ctx);
	}
	lastL = "T";
	coolX = thePaths[i][1];
	coolY = thePaths[i][2];
	return tmpWord;
}
function svgA(thePaths, i, ctx) {
	lastL = "A";
	coolX = thePaths[i][6];
	coolY = thePaths[i][7];
	return slowMove2(thePaths[i][6], thePaths[i][7], ctx);  //W.I.P.
}
function svgZ(thePaths, i, ctx) {
	lastL = "Z";
	return raiseBit2(ctx);
}

function check(thePaths, i, num){
	if (thePaths[i].length < num) {
		return true;
	} else {
		console.log("too long");
		return false;
	}
}



function raiseBit2(ctx) {
	return " Z ";
}
function fastMove2(coolX, coolY, ctx) {
	ctx.moveTo(coolX,coolY);
	if (first == false) {
		return "M " + coolX + " " + coolY;
		first = true;
	} else {
		return " M " + coolX + " " + coolY;
	}
}
function slowMove2(coolX, coolY, ctx) {
	ctx.lineTo(coolX,coolY);
	return " L " + Math.round(coolX,2) + " " + Math.round(coolY,2);
}
