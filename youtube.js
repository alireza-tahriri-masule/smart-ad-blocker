(() => {
  // Check if the script is running on YouTube
  if (window.location.hostname.includes("youtube.com")) {
    console.log("[Ad Skipper] Extension initialized on YouTube");

    // Function to handle skipping ads
    const skipAds = () => {
      const skipButton = document.querySelector(".ytp-skip-ad-button");
      if (skipButton) {
        skipButton.style.display = "block";
        skipButton.click();
        console.log("[Ad Skipper] Ad skipped!");
      }
    };

    // Set an interval to check for ads
    const CHECK_INTERVAL = 500; // Check every 500ms
    const intervalId = setInterval(skipAds, CHECK_INTERVAL);

    // Clean up the interval when navigating away from YouTube
    window.addEventListener("beforeunload", () => {
      clearInterval(intervalId);
      console.log("[Ad Skipper] Extension stopped as user navigated away.");
    });
  }
})();