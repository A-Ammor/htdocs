var content = document.getElementById("getallenrij");
var start = document.getElementById("start");
var show = document.getElementById("show");
var punten = document.getElementById("punten");
var getallen = []
var aantalClicks = 0;
var aantalPunten = 0;
var puntenTellen = false;

createStartButton();
createShowButton();

function createStartButton() {
    start.addEventListener("click", function() {
        showPunten(0);
        puntenTellen = true;
        getalhouders = document.getElementsByClassName("getalhouder");
        for(var x = 0; x < getalhouders.length; x++) {
            getalhouders[x].style.color = "white";
        }
    })
}

function createShowButton() {
    show.addEventListener("click", function() {
        puntenTellen = false;
        aantalClicks = 0;
        showPunten(0);
        createGetallen();
        removeGetalhouders();
        appendGetalhouders();
        console.log(getalhouders);
        getalhouders = document.getElementsByClassName("getalhouder");
        for (var x = 0; x < getalhouders.length; x++) {
            getalhouders[x].style.color = "black";
        }
    });
}

function createGetalhouder(i) {
    var getalhouder = document.createElement("div");
    var getal = document.createElement("p");
    getalhouder.className = "getalhouder";
    getalhouder.id = "getalhouder" + i;
    getal.innerText = getallen[i];
    getal.className = "getal";
    getalhouder.appendChild(getal);
    getalhouder.addEventListener("click", function () {
        checkClick(i);
    });
    return getalhouder;
 }

function appendGetalhouders() {
    var i = 0;
    for (var y = 1; y <= 4; y++) {
        for (var x = 1; x <= 4; x++) {
            getalhouder = createGetalhouder(i)
            getalhouder.style.left = (x * 150) + "px";
            getalhouder.style.top = (y * 150) + "px";
            content.appendChild(getalhouder);
            i++;
        }
    }
}
function removeGetalhouders() {
    getalhouders = document.getElementsByClassName("getalhouder");
    console.log("Getalhouders voor remove " + getalhouders);
    var len = getalhouders.length
    for (var x = 0; x < len; x++) {
        document.getElementById("getalhouder" + x).remove();
    }
}


function showPunten(p) {
    aantalPunten = p;
    punten.innerText = "Aantal punten is: " + aantalPunten;
}

function checkClick(i) {
    aantalClicks++;
    console.log( "getalhouder" + i + " waarde = " + getallen[i] + " aantal clicks = " + aantalClicks);
    if(getallen[i] == aantalClicks) {
        if(puntenTellen) {
            aantalPunten++;
        }
        showPunten(aantalPunten);
        getalhouder = document.getElementById("getalhouder"+i);
        getalhouder.style.color = "red";
    } else {
        getalhouders = document.getElementsByClassName("getalhouder");
        for(var x = 0; x < getalhouders.length; x++) {
            getalhouders[x].style.color = "black";
        }
    }
}

function createGetallen() {
    getallen = [];
    for(var i = 1; i <= 16; i++) {
        getallen.push(i);
    }
    return shuffle(getallen);
}

//Gepikt van ...https://www.jstips.co/en/javascript/shuffle-an-array/

function shuffle(arr) {
    var i,
        j,
        temp;
    for (i = arr.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }
    return arr;
};

