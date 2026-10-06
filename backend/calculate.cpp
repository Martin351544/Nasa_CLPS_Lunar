#include<bits/stdc++.h>
#include "api.hpp"

using namespace std;

string addOneDay(string date)
{
    int year = stoi(date.substr(0, 4));
    int month = stoi(date.substr(5, 2));
    int day = stoi(date.substr(8, 2));

    day++;

    // Days in each month
    int daysInMonth[] = {
        31, 28, 31, 30, 31, 30,
        31, 31, 30, 31, 30, 31
    };

    // Leap year
    if (month == 2 &&
        (year % 400 == 0 || (year % 4 == 0 && year % 100 != 0)))
    {
        daysInMonth[1] = 29;
    }

    // If we passed the end of the month
    if (day > daysInMonth[month - 1])
    {
        day = 1;
        month++;

        // If we passed December
        if (month > 12)
        {
            month = 1;
            year++;
        }
    }

    // Rebuild YYYY-MM-DD
    string result =
        to_string(year) + "-" +
        (month < 10 ? "0" : "") + to_string(month) + "-" +
        (day < 10 ? "0" : "") + to_string(day);

    return result;
}

// adds 1 hour to get the stop time the API needs
string addOneHour(string date, string time) {
    int hour = stoi(time.substr(0, 2));
    int min = stoi(time.substr(3, 2));

    hour ++;

    if (hour >= 24) {
        hour = 0;
        
        date = addOneDay(date);
    }

    string stopTime = (hour < 10? "0": "") + to_string(hour) + 
        ":" + 
        (min < 10 ? "0" : "") + to_string(min);

    return date + " " + stopTime;
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

    // calc stop time & date
    string stopDateTime = addOneHour(date, time);

    // Split stopDateTime into date and time
    string stopDate = stopDateTime.substr(0, 10);
    string stopTime = stopDateTime.substr(11, 5);

    // Print the received values for testing.
    cout << "Latitude: " << latitude << '\n';
    cout << "Longitude: " << longitude << '\n';
    cout << "Start: " << date << " " << time << '\n';
    cout << "Stop:  " << stopDate << " " << stopTime << '\n';

    // Create NASA Horizons URL
    string url =
        "https://ssd.jpl.nasa.gov/api/horizons.api"
        "?format=json"
        "&COMMAND=301"
        "&EPHEM_TYPE=VECTORS"
        "&CENTER=500%40399"
        "&START_TIME=" + date + "%20" + time +
        "&STOP_TIME=" + stopDate + "%20" + stopTime +
        "&STEP_SIZE=1%20h";

    cout << "DEBUG URL " << url << endl;

    // build API url
    // call the func
    string response = callAPI(url);

    cout << response << '\n'; // later we will parse the json and output it on the site
    
    return 0;
}
