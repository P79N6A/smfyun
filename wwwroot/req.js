var request = require('request');

for (var i=0; i<100000; i++) {
 console.info('a'+i);
 request('https://www.baidu.com', function (error, response, body) {
    if (error !== null) console.log('error:', error); // Print the error if one occurred
   console.log('statusCode:', response && response.statusCode); // Print the response status code if a response was received
    console.log('body:', body); // Print the HTML for the Google homepage.
 });
}

console.log('b'+i);
