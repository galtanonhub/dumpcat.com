module.exports = function (eleventyConfig) {
  eleventyConfig.addFilter("isoDate", (d) =>
    (d ? new Date(d) : new Date()).toISOString().slice(0, 10)
  );
  eleventyConfig.addFilter("limit", (arr, n) => arr.slice(0, n));
  eleventyConfig.addShortcode("year", () => `${new Date().getFullYear()}`);

  eleventyConfig.addPassthroughCopy("src/style.css");
  eleventyConfig.addPassthroughCopy("src/*.webp");
  eleventyConfig.addPassthroughCopy("src/favicon.svg");
  eleventyConfig.addPassthroughCopy("src/robots.txt");
  eleventyConfig.addPassthroughCopy("src/404.html");
  eleventyConfig.ignores.add("src/404.html");

  return {
    pathPrefix: process.env.PATH_PREFIX || "/samples/template-07/",
    dir: {
      input: "src",
      includes: "_includes",
      data: "_data",
      output: "_site",
    },
    htmlTemplateEngine: "njk",
    markdownTemplateEngine: "njk",
  };
};
