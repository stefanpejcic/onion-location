import sys
from urllib.request import urlopen
from bs4 import BeautifulSoup

def get_onion_location(url):
    """
    Get website from `Onion-Location` header of a website
    """
    if not url.startswith("https://"):
        return None
    with urlopen(url) as resp:
        onion_location = resp.getheader("onion-location")
        if onion_location:
            return onion_location
        soup = BeautifulSoup(resp, "html.parser")
        tag = soup.find("meta", attrs={"http-equiv": "onion-location"})
        if tag:
            return tag.get("content")
    return None

if __name__ == "__main__":
    for url in sys.argv[1:]:
        print(get_onion_location(url))
