<div class="data-sidebar">

<h2 class="canvas-sidebar-title">Information</h2>

<div class=""> 
	<div class=""> 
		<ul class="sections list block-list"> 
			<li class="sections__item"> 
				<div class="label"><p>To use this app correctly make sure to give your sections and variations meaningful names.</p> </div>
			</li>
			<li class="sections__item"> 
				<div class="label"><p>Uncheck the combinations you want to disable and press 'Save'.</p> </div>
			</li> 
			<li class="sections__item" data-test-section="collaborators-change-role"> 
				<div class="label"><p>Unchecking a combination only applies to new visitors, visitors who have already been bucketed into a combination will keep seeing that combination until the experiment is stopped.</p> </div>
			</li>
		</ul>
	</div>
</div>

<h2 class="canvas-sidebar-title">Settings</h2>

<div class=""> 
	<div class=""> 
		<ul class="sections list block-list"> 
			<form id="disable-app" method="POST" action="index.php">
				<input type="hidden" name="action" id="action"/>
				<li class="sections__item" data-test-section="integration-sidebar-on-off-section" id="disable-toggle"> <div class="button-group"> <button class="button enable <?php if ($canvas->is_enabled()) {echo('button--highlight');} ?>">On</button> <button class="button disable <?php if (!$canvas->is_enabled()) {echo('button--highlight');} ?>">Off</button> </div> </li>
			</form>
		</ul>
	</div>
</div>

</div>