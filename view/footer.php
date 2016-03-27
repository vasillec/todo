<?php if(isset($_SESSION["logged_in"])) : ?>
</section>
<footer>
	<button id="addTodoList" type="button" class="blue-btn" onclick="Add_project();"><span>Add TODO List</span></button>
</footer>
<?php endif; ?>
<div id="errorBlock" class="errorBlock">
	<span class="exit" title="close">✖</span>
	<span>ERROR! </span>
	<span class="error-message"></span>
</div>
<div id="hide-layout" class="hide-layout"></div>
</body>
</html>
