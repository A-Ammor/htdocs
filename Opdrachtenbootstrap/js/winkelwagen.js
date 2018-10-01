
function berekenSubTotaal(prijsinput, aantalinput, subtotaal){
	var prijs = parseFloat(prijsinput.textContent);
	var aantal = parseInt(aantalinput.value);
	var totaalprijs = prijs * aantal;
	subtotaal.textContent = totaalprijs.toFixed(2);
}

//Smarties
var smarties_price = document.getElementById("smarties_price");
var smarties_aantal = document.getElementById("smarties_aantal");
var smarties_totaal = document.getElementById("smarties_totaal");

smarties_aantal.addEventListener("change", function() {
	berekenSubTotaal(smarties_price, smarties_aantal, smarties_totaal);
});


//Kerst Stokjes
var kerststokjes_price = document.getElementById("kerststokjes_price");
var kerststokjes_aantal = document.getElementById("kerststokjes_aantal");
var kerststokjes_totaal = document.getElementById("kerststokjes_totaal");

kerststokjes_aantal.addEventListener("change", function() {
	berekenSubTotaal(kerststokjes_price, kerststokjes_aantal, kerststokjes_totaal);
});


//Super Candy Star
var supercandy_price = document.getElementById("supercandy_price");
var supercandy_aantal = document.getElementById("supercandy_aantal");
var supercandy_totaal = document.getElementById("supercandy_totaal");

supercandy_aantal.addEventListener("change", function() {
	berekenSubTotaal(supercandy_price, supercandy_aantal, supercandy_totaal);
});

//1.50
totaalknop.addEventListener("click", function() {
	//iets doen!

	var totaalprijs = (parseFloat(smarties_totaal.textContent) 
					+ parseFloat(kerststokjes_totaal.textContent) 
					+ parseFloat(supercandy_totaal.textContent));
	

	if(document.getElementById("snoepemmer").checked){
	 totaalprijs = totaalprijs + 1.50;
	}

	if(document.getElementById("tandenborstel").checked){
		totaalprijs = totaalprijs + 24.95;
	}
	if(document.getElementById("radiobutton").checked){
		totaalprijs = totaalprijs +  4.95;
	}
	document.getElementById("totaalprijs").textContent = totaalprijs.toFixed(2);
});
