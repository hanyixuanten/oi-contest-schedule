#!/usr/bin/env sh

set -eu

plugin_slug="oi-contest-schedule"
plugin_file="${plugin_slug}.php"
script_dir="$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)"
build_dir="${script_dir}/build"
plugin_dir="${build_dir}/${plugin_slug}"

clean() {
	find "${script_dir}" -maxdepth 1 -type f -name "${plugin_slug}-*.zip" -delete
	rm -rf "${build_dir}"
}

build() {
	for command in grep sed msgfmt zip unzip; do
		if ! command -v "${command}" >/dev/null 2>&1; then
			printf 'Required command not found: %s\n' "${command}" >&2
			exit 1
		fi
	done

	version="$(grep -E '^ \* Version:' "${script_dir}/${plugin_file}" | sed -E 's/^ \* Version: *//')"
	archive_name="${plugin_slug}-${version}.zip"
	archive_path="${script_dir}/${archive_name}"

	clean
	mkdir -p "${plugin_dir}"
	cp "${script_dir}/${plugin_file}" \
		"${script_dir}/uninstall.php" \
		"${script_dir}/readme.txt" \
		"${script_dir}/LICENSE" \
		"${plugin_dir}/"
	cp -R "${script_dir}/includes" "${plugin_dir}/"
	cp -R "${script_dir}/assets" "${plugin_dir}/"
	mkdir -p "${plugin_dir}/languages"
	cp "${script_dir}/languages/${plugin_slug}.pot" \
		"${script_dir}/languages/${plugin_slug}-zh_CN.po" \
		"${plugin_dir}/languages/"
	msgfmt --check --check-format \
		--output-file="${plugin_dir}/languages/${plugin_slug}-zh_CN.mo" \
		"${script_dir}/languages/${plugin_slug}-zh_CN.po"
	(
		cd "${build_dir}"
		zip -qr "${archive_path}" "${plugin_slug}"
	)

	unzip -p "${archive_path}" "${plugin_slug}/${plugin_file}" | grep -q '^ \* Plugin Name: OI Contest Schedule$'
	unzip -p "${archive_path}" "${plugin_slug}/readme.txt" | grep -q "^Stable tag: ${version}$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/languages/${plugin_slug}-zh_CN.mo$"
	if unzip -Z1 "${archive_path}" | grep -q '/wp-plugins-.*\.po$'; then
		printf 'Unexpected GlotPress submission file found in archive.\n' >&2
		exit 1
	fi
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/includes/class-contest-client.php$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/includes/class-plugin.php$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/includes/class-schedule-renderer.php$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/assets/css/schedule.css$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/assets/js/schedule.js$"
	unzip -Z1 "${archive_path}" | grep -q "^${plugin_slug}/assets/js/schedule-editor.js$"
	if unzip -Z1 "${archive_path}" | grep -Eq '/README(\.zh-CN)?\.md$'; then
		printf 'Unexpected development README found in archive.\n' >&2
		exit 1
	fi

	printf 'Built %s\n' "${archive_path}"
}

case "${1:-build}" in
	build)
		build
		;;
	clean)
		clean
		printf 'Removed local build artifacts.\n'
		;;
	*)
		printf 'Usage: %s [build|clean]\n' "$0" >&2
		exit 2
		;;
esac