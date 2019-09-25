// For a detailed explanation regarding each configuration property, visit:
// https://jestjs.io/docs/en/configuration.html

module.exports = {

	preset: 'jest-puppeteer',

	// The glob patterns Jest uses to detect test files
	testMatch: [
		"**/*-test.js",
	],

	setupFilesAfterEnv: [
		'<rootDir>/bootstrap.js',
	]

};
