#include<bits/stdc++.h>
#include<curl/curl.h>
using namespace std;

// function called automatically by libcurl upon data
// being received
size_t WriteCallback(
        void* contents, // pointer to the received data
        size_t size, // size of each data
        size_t nmemb, // number of data received
        void* userp // pointer to our own data

) {
    // bytes received calc
    size_t total = size * nmemb;

    string* response = static_cast<string*>(userp);

    response->append(static_cast<char*>(contents), total);

    return total;
}

string callAPI(const string& url) {
    // create a libcurl obj
    CURL* curl = curl_easy_init();

    // empty string return if curl = none
    if (!curl) {
        return "";
    }

    string response;
    
    // tell libcurl which URL we want to request
    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());

    // tell libcurl which function should handle incoming 
    // request data
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    
    // tell libcurl what data we want to give to writecallback
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response);
    

    // perform the HTTP request
    CURLcode result = curl_easy_perform(curl);

    // check if the HTTP request failed
    if (result != CURLE_OK) {
        cerr << "REQUEST FAILED: " << curl_easy_strerror(result) << '\n';
    }

    curl_easy_cleanup(curl);

    return response;
}
