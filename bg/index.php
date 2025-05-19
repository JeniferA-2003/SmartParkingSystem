<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            padding-top: 20px;
        }
        #response,#deleteResponse {
            white-space: pre-wrap;
            background: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Smart Parking System backgroung service panel</h1>
        <button id="testBtn" class="btn btn-primary">Test API</button>
        <button id="startContinuousBtn" class="btn btn-secondary">Start Continuous Run</button>
        <div id="response"></div>
        <div id="deleteResponse"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        let isRunning = false;
        let apiCallCount = 0;
        let deleteApiCallCount = 0;

        $('#testBtn').click(function() {
            console.log("Testing API call");
            warnAPI();
            callDeleteAPI(); // Test the delete API concurrently
        });

        $('#startContinuousBtn').click(function() {
            if (!isRunning) {
                console.log("Starting continuous API calls");
                $(this).text('Stop Continuous Run');
                isRunning = true;
                contiWarnAPI();
                contiDeleteAPI(); // Start continuous calls for delete API
            } else {
                console.log("Stopping continuous API calls");
                $(this).text('Start Continuous Run');
                isRunning = false;
            }
        });

        function warnAPI() {
            apiCallCount++;
            $.ajax({
                url: 'ParkedVehicalWarning.php', // URL to the PHP script
                type: 'GET',
                success: function(response) {
                    console.log("API call successful", response);
                    $('#response').html(`API Call ${apiCallCount}: ` + JSON.stringify(response, null, 4));
                },
                error: function(xhr, status, error) {
                    console.error("API call failed", error);
                    $('#response').html(`API Call ${apiCallCount}: Error - ` + error);
                }
            });
        }

        function callDeleteAPI() {
            deleteApiCallCount++;
            $.ajax({
                url: 'DeleteReservation.php', // URL for DeleteReservation API
                type: 'GET',
                success: function(response) {
                    console.log("Delete API call successful", response);
                    $('#deleteResponse').html(`Delete API Call ${deleteApiCallCount}: ` + JSON.stringify(response, null, 4));
                },
                error: function(xhr, status, error) {
                    console.error("Delete API call failed", error);
                    $('#deleteResponse').html(`Delete API Call ${deleteApiCallCount}: Error - ` + error);
                }
            });
        }

        function contiWarnAPI() {
            if (isRunning) {
                warnAPI(); // Reuse the warnAPI function which updates the counter
                setTimeout(contiWarnAPI, 1000); // Schedule the next call
            }
        }

        function contiDeleteAPI() {
            if (isRunning) {
                callDeleteAPI(); // Reuse the callDeleteAPI function which updates the counter
                setTimeout(contiDeleteAPI, 1000); // Schedule the next call
            }
        }
    });
</script>
</body>
</html>
