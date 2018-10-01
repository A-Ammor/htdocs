var content = document.getElementById("content");
content.style.position = "relative";
content.style.top = "10px";
content.style.height = "500px";
content.style.width = "500px";
content.style.margin = "auto";
content.style.marginBottom = "20px";
properimage = createCoordinates();
imagepos = shuffle(deepCopy(properimage));
var imageSelected = -1;
var punten = 0;

appendImageholders();

function start() {

}

function appendImageholders() {
    var i = 0;
    for (var y = 0; y <= 4; y++) {
        for (var x = 0; x <= 4; x++) {
            xmove = x * 100;
            ymove = y * 100;
            //xpos = (x - 1)* 25;
            //ypos = (y - 1) * 25;
            xpos = imagepos[i].x;
            ypos = imagepos[i].y;
            console.log(xpos + " " + ypos);
            var imageholder = createImageholder(i)
            imageholder.style.left = xmove + "px";
            imageholder.style.top = ymove + "px";
            imageholder.style.backgroundImage = "url('../pictures/ritapuzzle.jpg')";
            imageholder.style.backgroundPosition = xpos + "%" + ypos + "%" ;
            content.appendChild(imageholder);
            i++;
        }
    }
}

function createImageholder(i) {
    var imageholder = document.createElement("div");
    imageholder.className = "imageholder";
    imageholder.id = "imageholder" + i;
    imageholder.addEventListener("click", function () {
        swapImages(i);
    });
    return imageholder;
}

function swapImages(i) {
    //is er al een image geselecteerd?
    console.log("ImageSelected: " + imageSelected);
    if(imageSelected == -1) {
        //no image selected -- set imageselected to i
        imageSelected = i;
    } else {
        swap(imagepos, imageSelected, i);
        imageSelected = -1;
        //per swap komt er een punt bij....
        punten++;
        updateScore();
        //Gewonnen? Plaatje compleet?
        if(compareArrays(imagepos, properimage)) {
            document.getElementById("punten").innerHTML = "<h1>Yeah, you nailed it in " + punten + " swaps</h1>";
        }
    }
    //opnieuw background-position instellen voor alle imageholders
    var imageholders = document.getElementsByClassName("imageholder");
    for(var i = 0; i < imageholders.length; i++) {
        document.getElementById("imageholder" + i).style.backgroundPosition = imagepos[i].x + "% " + imagepos[i].y + "%";
    }
}

function compareArrays(foo, bar) {
    var compare = 0;
    for(var a = 0; a < foo.length; a++) {
        if(foo[a].x == bar[a].x) {
            compare++;
            console.log(foo[a].x + " " + bar[a].x);
        }
    }
    if(foo.length == compare) {
        return true;
    } else {
        return false;
    }
}

function createCoordinates() {
    var coordinates = [];
    for(var a = 0; a <= 4; a++) {
        for(var b = 0; b <= 4; b++) {
            coordinates.push({x: (b * 25), y: (a * 25)});
        }
    }
    return coordinates;
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
}

function swap(arr, indexA, indexB) {
    var temp = arr[indexA];
    arr[indexA] = arr[indexB];
    arr[indexB] = temp;
    return arr;
}

function deepCopy(arr) {
    var out = [];
    for (var i = 0, len = arr.length; i < len; i++) {
        var item = arr[i];
        var obj = {};
        for (var k in item) {
            obj[k] = item[k];
        }
        out.push(obj);
    }
    return out;
}

function updateScore() {
    var scoreBoard = document.getElementById("punten");
    scoreBoard.innerHTML = "<h3>Score: " + punten + "</h3>";
}