export const CELL_KEY_SEPARATOR = ':';

export function cellKey(groupId, specialtyId) {
	return `${groupId}${CELL_KEY_SEPARATOR}${specialtyId}`;
}

export function parseCellKey(key) {
	const separatorIndex = String(key).lastIndexOf(CELL_KEY_SEPARATOR);
	if (separatorIndex < 1) {
		return null;
	}

	return {
		groupId: Number(String(key).slice(0, separatorIndex)),
		specialtyId: Number(String(key).slice(separatorIndex + 1)),
	};
}

export function specialtyColumnLabel(specialty) {
	if (specialty?.column_label) {
		return specialty.column_label;
	}

	const description = String(specialty?.description || '').trim();
	if (!description) {
		return '—';
	}

	return description.length <= 14
		? description.toUpperCase()
		: description
				.split(/\s+/)
				.map((w) => w.charAt(0))
				.join('')
				.slice(0, 6)
				.toUpperCase();
}
