<!DOCTYPE html>
<html>
<head>
<style type="text/css">
div.dice {
	float: left;
	width:32px;
	background:#F5F5F5;
	border:#999 1px solid;
	padding: 7.5px 7.5px 7.5px 7.5px;
	font-size:24px;
	text-align:center;
	margin:5px;
}
button.dice {
	float: left;
	margin-top: 14px;
	margin-left: 10.25px;
	margin-right: 10.25px;
	text-align: center;
}
div.slice {
	float: right;
	width:32px;
	background:#F5F5F5;
	border:#999 1px solid;
	padding: 7.5px 7.5px 7.5px 7.5px;
	font-size:24px;
	text-align:center;
	margin:5px;
}
button.slice {
	float: right;
	margin-top: 14px;
	margin-left: 10.25px;
	margin-right: 10.25px;
	text-align: center;
}
#wrapper{
	margin-right: 
	width:1000px;
}
#player1 {
	width: 50%;
	float: left;
}
#player2{
	width: 50%;
	float: right;
}
#buttonl {
	float: right;
}
#buttonReset {
	position: absolute;
	float: center;
}
#statusS{
	position: relative;
	float: right;
}
#statusD{
	position: relative;
	float: left;
}
</style>
<script>
function rollDice(){
	var die1 = document.getElementById("die1");
	var die2 = document.getElementById("die2");
	var die3 = document.getElementById("die3");
	var die4 = document.getElementById("die4");
	var die5 = document.getElementById("die5");
	var statusD = document.getElementById("statusD");
	var d1 = Math.floor(Math.random() * 6) + 1;
	var d2 = Math.floor(Math.random() * 6) + 1;
	var d3 = Math.floor(Math.random() * 6) + 1;
	var d4 = Math.floor(Math.random() * 6) + 1;
	var d5 = Math.floor(Math.random() * 6) + 1;
	$maxNum = 30;
	$somD = d1 + d2 + d3 + d4 + d5;
	var diceTotal = $somD;
	die1.innerHTML = d1;
	die2.innerHTML = d2;
	die3.innerHTML = d3;
	die4.innerHTML = d4;
	die5.innerHTML = d5;
	statusD.innerHTML = "Player 1 rolled "+diceTotal+".";

	if($somD == $maxNum){
		statusD.innerHTML += " Gefeliciteerd, alles 6!!";
	}
}

function rollSlice(){
	var sli1 = document.getElementById("sli1");
	var sli2 = document.getElementById("sli2");
	var sli3 = document.getElementById("sli3");
	var sli4 = document.getElementById("sli4");
	var sli5 = document.getElementById("sli5");
	var statusS = document.getElementById("statusS");
	var s1 = Math.floor(Math.random() * 6) + 1;
	var s2 = Math.floor(Math.random() * 6) + 1;
	var s3 = Math.floor(Math.random() * 6) + 1;
	var s4 = Math.floor(Math.random() * 6) + 1;
	var s5 = Math.floor(Math.random() * 6) + 1;
	$maxNumS = 30;
	$somS = s1 + s2 + s3 + s3 + s5;
	var diceTotalS = $somS;
	sli1.innerHTML = s1;
	sli2.innerHTML = s2;
	sli3.innerHTML = s3;
	sli4.innerHTML = s4;
	sli5.innerHTML = s5;

	statusS.innerHTML = "Player 2 rolled "+diceTotalS+".";

	if($somS == $maxNumS){
		statusS.innerHTML += " Gratz all 6!!";
	}
}
</script>
	<title>Dice game</title>
</head>
<body>
<div id="wrapper">
	<div id="player1">
		<!-- dice 1-->
		<div id="die1" class="dice">0</div>
		<div id="die2" class="dice">0</div>
		<div id="die3" class="dice">0</div><br><br><br>
		<div id="die4" class="dice">0</div>
		<button onclick="rollDice()" class="dice">P1</button>
		<div id="die5" class="dice">0</div>
		<h2 id="statusD" style="clear:left;"></h2>
	</div>
	<div id="player2">
		<!-- dice2 (slice) -->
		<div id="sli1" class="slice">0</div>
		<div id="sli2" class="slice">0</div>
		<div id="sli3" class="slice">0</div><br><br><br>
		<div id="sli4" class="slice">0</div>
		<button id="buttonl" onclick="rollSlice()" class="slice">P2</button>
	<div id="sli5" class="slice">0</div>
		<h2 id="statusS" style="clear:right;"></h2>
</div>
<form action="dice.php" method="get">
	<button id="buttonReset" >Reset</button>
</form>
</div>
</body>
</html>