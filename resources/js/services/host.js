export const isSubdomain = () => {
  let url = window.location.host;
  const hostname = typeof window !== "undefined" ? window.location.hostname : "";
  // Local development: only "localhost" or "127.0.0.1" (no subdomain) = central app
  // So "secundaria87.localhost" or "secundaria87.localhost:8000" = tenant app
  if ((hostname === "localhost" || hostname === "127.0.0.1") && !hostname.includes(".")) {
    return false;
  }
  if (url === "localhost" || url === "127.0.0.1" || url.startsWith("127.0.0.1:") || url.startsWith("localhost:")) {
    return false;
  }
  // IF THERE, REMOVE WHITE SPACE FROM BOTH ENDS
  url = url.replace(new RegExp(/^\s+/), ""); // START
  url = url.replace(new RegExp(/\s+$/), ""); // END

  // IF FOUND, CONVERT BACK SLASHES TO FORWARD SLASHES
  url = url.replace(new RegExp(/\\/g), "/");

  // IF THERE, REMOVES 'http://', 'https://' or 'ftp://' FROM THE START
  url = url.replace(new RegExp(/^http\:\/\/|^https\:\/\/|^ftp\:\/\//i), "");

  // IF THERE, REMOVES 'www.' FROM THE START OF THE STRING
  url = url.replace(new RegExp(/^www\./i), "");

  // REMOVE COMPLETE STRING FROM FIRST FORWARD SLASH ON
  url = url.replace(new RegExp(/\/(.*)/), "");

  // REMOVES '.??.??' OR '.???.??' FROM END - e.g. '.CO.UK', '.COM.AU'
  if (url.match(new RegExp(/\.[a-z]{2,3}\.[a-z]{2}$/i))) {
    url = url.replace(new RegExp(/\.[a-z]{2,3}\.[a-z]{2}$/i), "");
    // REMOVES '.??' or '.???' or '.????' FROM END - e.g. '.US', '.COM', '.INFO', '.ONLINE'
  } else if (url.match(new RegExp(/\.[a-z]{2,6}$/i))) {
    url = url.replace(new RegExp(/\.[a-z]{2,6}$/i), "");
  }

  // IP address (e.g. 127.0.0.1): not a subdomain
  if (/^\d{1,3}(\.\d{1,3}){3}$/.test(url)) {
    return false;
  }

  // CHECK TO SEE IF THERE IS A DOT '.' LEFT IN THE STRING
  var subDomain = url.match(new RegExp(/\./g)) ? true : false;

  localStorage.setItem("tenant", url.split(".")[0]);

  return subDomain;
};
