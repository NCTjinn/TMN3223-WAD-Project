function testConnection() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "testConnection.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('result').innerHTML = '<p>' + xhr.responseText + '</p>';
        } else if (xhr.readyState === 4 && xhr.status !== 200) {
            document.getElementById('result').innerHTML = '<p>Failed to connect to database.</p>';
        }
    };
    xhr.send();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("Current URL:", window.location.href);
  });
