export const changeTimeZone = (timeZone) => {
  let chicago_datetime_str = new Date().toLocaleString("en-US", {
    timeZone: timeZone,
  });
  let date_chicago = new Date(chicago_datetime_str);
  let year = date_chicago.getFullYear();
  let month = ("0" + (date_chicago.getMonth() + 1)).slice(-2);
  let date = ("0" + date_chicago.getDate()).slice(-2);
  let date_time = year + "-" + month + "-" + date;

  return date_time;
};
