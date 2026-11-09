#include<bits/stdc++.h>
#include "api.hpp"
#include <nlohmann/json.hpp> 

using namespace std;

string addOneMinute(string time) {
    int hours = stoi(time.substr(0, 2));
    int minutes = stoi(time.substr(3, 2));

    minutes++;

    if (minutes == 60) {
        minutes = 0;
        hours++;
    }

    if (hours == 24) {
        hours = 0;
    }

    string result =
        (hours < 10 ? "0" : "") + to_string(hours) + ":" +
        (minutes < 10 ? "0" : "") + to_string(minutes);

    return result;
}

int main(int argc, char* argv[]) {
    
    // We need:
    // argv[0] = program name
    // argv[1] = latitude
    // argv[2] = longitude
    // argv[3] = date
    // argv[4] = time


    // check if PHP inputs have been given
    if (argc != 5) {
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

    // Create NASA Horizons URL for the sun and the earth
    string SunUrl =
        "https://ssd.jpl.nasa.gov/api/horizons.api"
        "?format=json"
        "&COMMAND='10'"
        "&EPHEM_TYPE='OBSERVER'"
        "&CENTER='coord@301'"
        "&COORD_TYPE='GEODETIC'"
        "&SITE_COORD='" + to_string(longitude) + "," +
                           to_string(latitude) + ",0'"
        "&START_TIME='" + date + "%20" + time + "'"
        "&STOP_TIME='" + date + "%20" + addOneMinute(time) + "'"
        "&STEP_SIZE='1%20h'"
        "&QUANTITIES='4'";   
    
    string EarthUrl =
        "https://ssd.jpl.nasa.gov/api/horizons.api"
        "?format=json"
        "&COMMAND='399'"
        "&EPHEM_TYPE='OBSERVER'"
        "&CENTER='coord@301'"
        "&COORD_TYPE='GEODETIC'"
        "&SITE_COORD='" + to_string(longitude) + "," +
                           to_string(latitude) + ",0'"
        "&START_TIME='" + date + "%20" + time + "'"
        "&STOP_TIME='" + date + "%20" + addOneMinute(time) + "'"
        "&STEP_SIZE='1%20h'"
        "&QUANTITIES='4'";


    // build API url
    // call the API for the sunUrl
    string SunResponse = callAPI(SunUrl);
    string EarthResponse = callAPI(EarthUrl);
   

    nlohmann::json sunData = nlohmann::json::parse(SunResponse);
    nlohmann::json earthData = nlohmann::json::parse(EarthResponse);
        
    string sunResult = sunData.at("result").get<string>();
    string earthResult = earthData.at("result").get<string>();

    nlohmann::json output;
    output["sun"] = sunResult;
    output["earth"] = earthResult;

    cout << output.dump() << endl;
    return 0;
}
