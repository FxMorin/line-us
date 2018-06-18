function GCodeVars(gcode) {
  var setX = false;
  var setY = false;
  var lowX = 0;
  var lowY = 0;
  var highX = 0;
  var highY = 0;
  var tmp = "";
  var loopX = false;
  var loopY = false;
  for (var y = 0; y < gcode.length; y++) {
    if (loopX) {
      if (gcode.charAt(y) != " ") {
        tmp += gcode.charAt(y);
      } else {
        if (setX != true) {
          highX = parseInt(tmp);
          lowX = parseInt(tmp);
          setX = true;
        } else {
          if (parseInt(tmp) > highX) {
            highX = parseInt(tmp);
          } else if (parseInt(tmp) < lowX) {
            lowX = parseInt(tmp);
          }
        }
        tmp = "";
        loopX = false;
        loopY = false;
      }
    }
    if (loopY) {
      if (gcode.charAt(y) != " ") {
        tmp += gcode.charAt(y);
      } else {
        if (setY != true) {
          highY = parseInt(tmp);
          lowY = parseInt(tmp);
          setY = true;
        } else {
          if (parseInt(tmp) > highY) {
            highY = parseInt(tmp);
          } else if (parseInt(tmp) < lowY) {
            lowY = parseInt(tmp);
          }
        }
        tmp = "";
        loopY = false;
        loopX = false;
      }
    }
    if (gcode.charAt(y) == "X") {
      loopX = true;
    } else if (gcode.charAt(y) == "Y") {
      loopY = true;
    }
  }
  var validate = highX + " " + lowX + " " + highY + " " + lowY;
  return validate.split(" ");
}
function GCodeRatio(gcode, ratio) {
  var tmp = "";
  var loop = false;
  var char = "";
  var theChar = "";
  var output = "";
  for (var y = 0; y < gcode.length; y++) {
    if (loop) {
      if (gcode.charAt(y) != " ") {
        tmp += gcode.charAt(y);
      } else {
        if (theChar === "G") {
          output += "G" + tmp;
        } else if (theChar === "X") {
          output += " X" + Math.round(parseInt(tmp)*ratio);
        } else if (theChar === "Y") {
          output += " Y" + Math.round(parseInt(tmp)*ratio);
        } else if (theChar === "Z") {
          output += " Z" + tmp;
        }
        tmp = "";
        loop = false;
      }
    } else {
      char = gcode.charAt(y);
      if (char === "G" || char === "X" || char === "Y" || char === "Z") {
        loop = true;
        theChar = char;
      }
    }
  }
  output += " Z1000";
  return output;
}
function GCodeSizes(gcode, ratio) {
  var sizes = GCodeVars(gcode);
  var moveRight = 0;
  var moveLeft = 0;
  var moveUp = 0;
  var moveDown = 0;
  if (sizes[0] > 1750) {
    moveLeft = sizes[0]-1750;
  } else if (sizes[1] < 650) {
    moveRIGHT = 650-sizes[1];
  }
  if (sizes[2] > 1000) {
    moveDown = sizes[2]-1000;
  } else if (sizes[3] < -1000) {
    moveUp = Math.abs(sizes[3])-1000;
  }
  var tmp = "";
  var loop = false;
  var char = "";
  var theChar = "";
  var output = "";
  for (var y = 0; y < gcode.length; y++) {
    if (loop) {
      if (gcode.charAt(y) != " ") {
        tmp += gcode.charAt(y);
      } else {
        if (theChar === "G") {
          output += "G" + tmp;
        } else if (theChar === "X") {
          output += " X" + (parseInt(tmp)+moveRight-moveLeft);
        } else if (theChar === "Y") {
          output += " Y" + (parseInt(tmp)+moveUp-moveDown);
        } else if (theChar === "Z") {
          output += " Z" + tmp;
        }
        tmp = "";
        loop = false;
      }
    } else {
      char = gcode.charAt(y);
      if (char === "G" || char === "X" || char === "Y" || char === "Z") {
        loop = true;
        theChar = char;
      }
    }
  }
  output += " Z1000";
  return output;
}
