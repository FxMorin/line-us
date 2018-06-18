function Bezier(a, c1, c2, b) {
      // output the curve in SVG bezier syntax
      svgBezier = `M ${a.coolX} ${a.coolY} C ${c1.x} ${c1.y}, ${c2.x} ${c2.y}, ${b.x} ${b.y}`,
      // create a new <path> element
      path = document.createElementNS("http://www.w3.org/2000/svg", "path");
  // add the curve
  path.setAttribute('d', svgBezier);
  // get the length using browser power
  return path.getTotalLength();
}
function BezierP(a, c1, c2, b, l) {
      // output the curve in SVG bezier syntax
      svgBezier = `M ${a.coolX} ${a.coolY} C ${c1.x} ${c1.y}, ${c2.x} ${c2.y}, ${b.x} ${b.y}`,
      // create a new <path> element
      path = document.createElementNS("http://www.w3.org/2000/svg", "path");
  // add the curve
  path.setAttribute('d', svgBezier);
  // get the length using browser power
  return path.getPointAtLength(l);
}

function Quadratic(a, c1, c2) {
      // output the curve in SVG bezier syntax
      svgBezier = `M ${a.coolX} ${a.coolY} Q ${c1.x} ${c1.y}, ${c2.x} ${c2.y}`;
      // create a new <path> element
      path = document.createElementNS("http://www.w3.org/2000/svg", "path");
  // add the curve
  path.setAttribute('d', svgBezier);
  // get the length using browser power
  return path.getTotalLength();
}
function QuadraticP(a, c1, c2, l) {
      // output the curve in SVG bezier syntax
      svgBezier = `M ${a.coolX} ${a.coolY} Q ${c1.x} ${c1.y}, ${c2.x} ${c2.y}`;
      // create a new <path> element
      path = document.createElementNS("http://www.w3.org/2000/svg", "path");
  // add the curve
  path.setAttribute('d', svgBezier);
  // get the length using browser power
  return path.getPointAtLength(l);
}
