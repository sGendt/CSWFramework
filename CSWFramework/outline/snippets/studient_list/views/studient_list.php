<?php if(count($rows) == 0): ?>
<p>Pas de compte participant, créer un compte participant: <a href="<?php CswString::p(URL . '/studient_create'); ?>">ici</a>.</p>
<?php else: ?>
<table>
	<tr>
		<th>Date de création</th>
		<th>Nom</th>
		<th>Login du compte</th>
		<th>Mot de passe du compte</th>
		<th>Dernière connexion</th>
		<th>Nombre de pages rédigées</th>
		<?php if(!empty($update)): ?><th>Modifier</th><?php endif; ?>
		<?php if(!empty($update)): ?><th>Supprimer</th><?php endif; ?>
	</tr>
	<?php foreach($rows as $row): ?>
	<tr>
		<td><?php CswString::p(date('d/m/Y', $row->time)); ?></td>
		<td><a href="<?php CswString::p(APPURL . '/?edit=' . $row->id); ?>"><?php  echo ($row->name) ? $row->name : 'Pas de nom'; ?></a></td>
		<td><?php CswString::p($row->login); ?></td>
		<td><?php CswString::p($row->password); ?></td>
		<td><?php echo ($row->lastConnection) ? (date('d/m/Y H:i', $row->lastConnection)) : ''; ?></td>
		<td><?php CswString::p($row->nbPages); ?></td>
		<?php if(!empty($update)): ?><td><a href="<?php CswString::p(URL . '/studient_create/?id=' . $row->id); ?>"><img src="<?php CswString::p(CswPref::pref('pathCdn') . 'images/button_update.png'); ?>" /></a></td><?php endif; ?>
		<?php if(!empty($update)): ?><td><a href="<?php CswString::p(URL . '/studient_list/?delete=true&studientId=' . $row->studientId); ?>"><img src="<?php CswString::p(CswPref::pref('pathCdn') . 'images/button_cancel.png'); ?>" /></a></td><?php endif; ?>
	</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>