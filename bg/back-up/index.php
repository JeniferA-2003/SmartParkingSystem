<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Task Runner</title>
    <script>
    async function triggerPHP() {
        try {
            console.log('Attempting to run DeleteReservation.php at:', new Date().toISOString());
            const startTime = Date.now();
            const response = await fetch('DeleteReservation.php');
            const data = await response.text();  // Assuming DeleteReservation.php outputs plain text or HTML
            const endTime = Date.now();
            const timeElapsed = endTime - startTime;
            console.log('PHP script response:', data, 'Processed in:', timeElapsed, 'ms');
            return timeElapsed; // Return the elapsed time for processing
        } catch (error) {
            console.error('Error calling PHP script:', error);
            return 0; // Return 0 if an error occurs
        }
    }

    async function startContinuousRun() {
        const minimumInterval = 5000; // Minimum interval between requests in milliseconds
        while (true) {  // Infinite loop to keep running
            const processingTime = await triggerPHP();  // Wait for the triggerPHP to complete and get processing time
            let nextCallDelay = minimumInterval - processingTime;  // Calculate delay for the next call
            nextCallDelay = nextCallDelay > 0 ? nextCallDelay : 0;  // Ensure non-negative delay
            console.log('Waiting for', nextCallDelay, 'ms before next call.');
            await new Promise(resolve => setTimeout(resolve, nextCallDelay)); // Wait for the calculated delay before the next call
        }
    }
</script>


</head>
<body>
    <h1>Background PHP Script Trigger</h1>
    <button onclick="startContinuousRun()">Start Continuous Run</button>
    <p>Check your console for output.</p>
</body>
</html>
