export function isServicioSpecialty(specialty) {
	if (!specialty) {
		return false;
	}

	const description = (specialty.description || '').trim().toUpperCase();
	const code = (specialty.code || '').trim().toUpperCase();

	return description === 'SERVICIO' || code === 'SERV';
}

export function isSinGrupoGroup(group) {
	if (!group) {
		return false;
	}

	const name = (group.name || group.group_label || '').trim().toUpperCase();

	return name === 'SIN GRUPO';
}
