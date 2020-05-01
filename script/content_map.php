<canvas id="map" style="border:1px solid #000; width:99%; " ></canvas>
<script type="text/javascript">

<?php require_once("vector.js"); ?>

const MOUSE_BUTTON_PRIMARY   = 1; // Primary button (usually the left button)
const MOUSE_BUTTON_SECONDARY = 2; // Secondary button (usually the right button)
const MOUSE_BUTTON_AUXILIARY = 4; // Auxilary button (usually the mouse wheel button or middle button)
const MOUSE_BUTTON_FOURTH    = 8; // 4th button (typically the "Browser Back" button)
const MOUSE_BUTTON_FIFTH     = 16; // 5th button (typically the "Browser Forward" button)

// GLOBAL VARIABLES
var Version = MakeVersion(0, 0, 1, 0);
console.log("Hatake v" + VersionToString(Version));

var point1 = new Vector(100, 100);
var point2 = new Vector(857, 991);

var GameState = [
	State_Initialize,
	State_MainLoop,
];
var CurrentGameState = GameState.lastIndexOf(State_Initialize);

var canvas = document.getElementById("map");
var ctx = canvas.getContext("2d");

var mapImage = new Image();
mapImage.src = '<?php print $projImage; ?>';

var width = 800;
var height = 448;
var keys = [];
var mouseWheel = 0;
var mouseButtons = 0;
var mouseMoveX = 0;
var mouseMoveY = 0;

var x = 0;

var mapScale = 1;
var mapSpanX = 0;
var mapSpanY = 0;

/** Application initialize */
function State_Initialize()
{
	canvas.width = width;
	canvas.height = height;
	
	CurrentGameState = GameState.lastIndexOf(State_MainLoop);
}

/** Main loop */
function State_MainLoop()
{
    ctx.clearRect(0, 0, width, height);
	
	if(mouseWheel > 0)
		mapScale *= 1.1;
	else if(mouseWheel < 0)
		mapScale /= 1.1;
	if(mapScale < 0.1)
		mapScale = 0.1;

	if(mouseButtons & MOUSE_BUTTON_AUXILIARY)
	{
		mapSpanX += mouseMoveX;
		mapSpanY += mouseMoveY;
	}
	
	ctx.drawImage(mapImage, mapSpanX, mapSpanY, mapImage.naturalWidth*mapScale, mapImage.naturalHeight*mapScale);

	ctx.fillStyle = "black";
	ctx.textAlign = "center";
	ctx.font = "64px Arial";
	ctx.fillText("Hatake", x, 120);
	x++;
	x %= width;

	ctx.beginPath();
	ctx.fillStyle = "#F44";	
	ctx.arc(mapSpanX + point1.x * mapScale, mapSpanY + point1.y * mapScale, 5, 0, 2 * Math.PI);
	ctx.fill();
	
	ctx.beginPath();
	ctx.fillStyle = "#F44";	
	ctx.arc(mapSpanX + point2.x * mapScale, mapSpanY + point2.y * mapScale, 5, 0, 2 * Math.PI);
	ctx.fill();
	
	ctx.strokeStyle = "#F44";
	ctx.strokeRect(mapSpanX + point1.x * mapScale, mapSpanY + point1.y * mapScale, mapSpanX + (point2.x - point1.x) * mapScale, mapSpanY + (point2.y - point1.y) * mapScale);

}

/** Main update function  */
function update()
{
    beginFrame();
	GameState[CurrentGameState]();
    endFrame();

    requestAnimationFrame(update);	
}

/***/
function MakeVersion(a, b, c, d)
{
	return (a << 24) + (b << 16) + (c << 8) + d;
}

/***/
function VersionToString(v)
{
	var r = ((v >> 24) & 0xFF) + "." + ((v >> 16) & 0xFF) + "." + ((v >> 8) & 0xFF) + "." + (v & 0xFF);
	return r;
}

/***/
function beginFrame()
{
	
}

/***/
function endFrame()
{
	mouseWheel = 0;
	mouseMoveX = 0;
	mouseMoveY = 0;
}



(function () {
    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
    window.requestAnimationFrame = requestAnimationFrame;
})();

document.body.addEventListener("keydown", function (e) {
    keys[e.keyCode] = true;
});

document.body.addEventListener("keyup", function (e) {
    keys[e.keyCode] = false;
});

document.body.addEventListener("wheel", function (e) {
    //console.info("[event] wheel: " + e.deltaY);
    const delta = Math.sign(e.deltaY);
	mouseWheel = delta;
});

document.body.addEventListener('mousedown', function(e) {
    //console.info("[event] mousedown: " + e.button + " / " + e.buttons );
	mouseButtons = e.buttons;
});

document.body.addEventListener("mouseup", function(e) {
    //console.info("[event] mouseup: " + e.button + " / " + e.buttons );
	mouseButtons = e.buttons;
});

document.body.addEventListener("mousemove", function(e) {
    //console.info("[event] mousemove: " + e.movementX + ":" + e.movementY );
	mouseMoveX = e.movementX;
	mouseMoveY = e.movementY;
});

document.body.addEventListener("contextmenu", function(e) {
    e.preventDefault();
});

window.addEventListener("load", function () {
	update();
});

</script>

<?php
	//print "<img id='map' src='$projImage ' style='width:100%;' />";
?>