// https://github.com/smooth-code/jest-puppeteer#jest-puppeteerconfigjs

let config = {
  launch: {
    headless: process.env.HEADLESS !== 'false',
  },
};

if ( false === config.launch.headless ) {
	config.launch.slowMo = 80;
}

module.exports = config;
