
<div class="alertJs display alert-<?php echo ($informationState->error) ? 'error' : 'info'; ?>">
	<?php if($informationState->error): ?>
	Afin de pouvoir publier votre ouvrage, vous devez remplir les conditions suivantes:
	<ul>
		<li><img src="<?php echo ($informationState->pages) ? $pathIcoState . 'button_cancel.png' : $pathIcoState . 'Check.png'; ?>" style="width: 10px; height: 10px;" /> Avoir rédigé un minimum de 30 pages</li>
		<li><img src="<?php echo ($informationState->bookInfosError) ? $pathIcoState . 'button_cancel.png' : $pathIcoState . 'Check.png'; ?>" style="width: 10px; height: 10px;" /> Avoir complété les informations de l'ouvrage
			<ul>
				<li>Titre</li>
				<li>Sous-titre</li>
				<li>Préface</li>
				<li>Résumé</li>
				<li>Image de couverture</li>
			</ul>
		</li>
		<li><img src="<?php echo ($informationState->studientPagesError) ? $pathIcoState . 'button_cancel.png' : $pathIcoState . 'Check.png'; ?>" style="width: 10px; height: 10px;" /> Tous les participants doivent avoir rédigé au moins une page</li>
		<li><img src="<?php echo ($informationState->studientInfosError) ? $pathIcoState . 'button_cancel.png' : $pathIcoState . 'Check.png'; ?>" style="width: 10px; height: 10px;" /> Tous les participants doivent avoir complété les informations auteur
			<ul>
				<li>Photographie</li>
				<li>Pseudonyme</li>
				<li>Nom</li>
				<li>Prénom</li>
				<li>Présentation</li>
			</ul>
		</li>
	</ul>
	<?php else: ?>
		<?php if($command->getPublished()): ?>
		<p>
			L'ouvrage à été publié le <?php CswString::p(date('d/m/Y', $command->getPublishTime())); ?>.
			<a href="<?php CswString::p(CswPref::pref('pathCdn') . 'medias/book/' . CswVar::v('formationId') . '/' . CswVar::v('formationId') . '.pdf'); ?>" class="btn">Télécharger l'ouvrage</a>
		</p>
		<?php else: ?>
		<p>Vous remplissez maintenant les conditions pour publier votre ouvrage.</p>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php if(!$informationState->error && !$command->getPublished()): ?>
<a href="<?php CswString::p(URL . '/book_preview'); ?>" class="btn">Publier</a>
<?php endif; ?>
