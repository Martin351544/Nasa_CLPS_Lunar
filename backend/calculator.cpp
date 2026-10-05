#include<bits/stdc++.h>
#include "api.hpp"

using namespace std;

int main(int argc, char* argv[]) {
    
    // check if PHP inputs have been given
    if (argc < 3) {
        cerr << "MISSING INPUT\n";
        return 1;
    }

    // Convert the latitude from a string into a number.
    double latitude = stod(argv[1]);

    // Convert the longitude from a string into a number.
    double longitude = stod(argv[2]);

    // Date and time stay as strings.
    string date = argv[3];
    string time = argv[4];


    // Print the received values for testing.
    cout << "Latitude: " << latitude << '\n';
    cout << "Longitude: " << longitude << '\n';
    cout << "Date: " << date << '\n';
    cout << "Time: " << time << '\n';

    /*
    Build the API URL here.

    For now this is just an example URL.

    We will replace the values below with the
    actual NASA/JPL API parameters once we decide
    exactly what lunar data your project needs.
    */

    string url =
        "https://ssd.jpl.nasa.gov/api/horizons.api"
        "?format=json";

    // call the func
    string response = callAPI(url);

    cout << response << '\n'; // later we will parse the json and output it on the site
    
    return 0;
}
