<!DOCTYPE html>
<html>
<body>

<h1>The XMLHttpRequest Object</h1>

<button type="button" onclick="loadDoc()">Request data</button>
<p id="data"></p>


<script>
    function loadDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
                document.getElementById("data").innerHTML = this.responseText;
        };
        xhttp.open("GET", "info.php", true);
        xhttp.send();
    }
</script>

</body>
</html>
