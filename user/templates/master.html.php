<% namespace Inkwell\HTML
{
	%>
		<!doctype html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

			<link rel="stylesheet" href="/style/theme.css" />
			<link rel="stylesheet" href="/style/layout.css" />
			<link rel="stylesheet" href="https://www.google.com/fonts#ChoosePlace:select/Collection:Source+Sans+Pro" />

			<title><%= html::esc($this['title'] ?: 'Welcome to Bustle') %></title>
		</head>
		<body>
			<% $this->insert('header') %>
			<% $this->insert('layout') %>
			<% $this->insert('footer') %>

			<script data-dojo-config="dojoBlankHtmlUrl:'/blank.html', baseUrl: '/', modulePaths: {local: 'script'}" src="//ajax.googleapis.com/ajax/libs/dojo/1.10.3/dojo/dojo.js"></script>
			<script src="/script/main.js"></script>

		</body>
		</html>
	<%
}
