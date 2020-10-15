setInterval(() => {
    $('#table_div').load('index.php' + ' #table_div');
}, 20000);

setInterval(() => {
    $('#table_status').load(window.location.href + ' #table_status > *');
}, 5000);