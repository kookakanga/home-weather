/*Tables required for home-weather
Author kookakanga (Bryce)
Last updated Nov 2021*/

CREATE TABLE timeObs (
    dateTim date,
    tim time,
    temperature double(5,3),
    pressure double(7,2),
    humidity double(5,2),
    windspeed double(4,2),
    gusts double(3,1),
    winddirection varchar(4)
);

CREATE TABLE rain (
    dateTim date,
    tim time, 
    rain double(5,4)
)