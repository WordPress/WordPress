#!/bin/bash
set -euo pipefail

cd "$(dirname "$(readlink -f "$BASH_SOURCE")")"

phpVersions=( "$@" )
if [ ${#phpVersions[@]} -eq 0 ]; then
	phpVersions=( php*.*/ )
fi
phpVersions=( "${phpVersions[@]%/}" )

current="$(curl -fsSL 'https://api.wordpress.org/core/version-check/1.7/' | jq -r '.offers[0].current')"
sha1="$(curl -fsSL "https://wordpress.org/wordpress-$current.tar.gz.sha1")"

cliVersion="$(
	git ls-remote --tags 'https://github.com/wp-cli/wp-cli.git' \
		| sed -r 's!^[^\t]+\trefs/tags/v([^^]+).*!\1!g' \
		| tail -1
)"
cliSha512="$(curl -fsSL "https://github.com/wp-cli/wp-cli/releases/download/v${cliVersion}/wp-cli-${cliVersion}.phar.sha512")"

declare -A variantExtras=(
	[apache]='\nRUN a2enmod rewrite expires\n'
	[fpm]=''
	[fpm-alpine]=''
	[cli]='' # unused
)
declare -A variantCmds=(
	[apache]='apache2-foreground'
	[fpm]='php-fpm'
	[fpm-alpine]='php-fpm'
	[cli]='' # unused
)
declare -A variantBases=(
	[apache]='debian'
	[fpm]='debian'
	[fpm-alpine]='alpine'
	[cli]='cli'
)

travisEnv=
for phpVersion in "${phpVersions[@]}"; do
	phpVersionDir="$phpVersion"
	phpVersion="${phpVersion#php}"

	for variant in apache fpm fpm-alpine cli; do
		dir="$phpVersionDir/$variant"
		mkdir -p "$dir"

		extras="${variantExtras[$variant]}"
		cmd="${variantCmds[$variant]}"
		base="${variantBases[$variant]}"

		entrypoint='docker-entrypoint.sh'
		if [ "$variant" = 'cli' ]; then
			entrypoint='cli-entrypoint.sh'
		fi

		(
			set -x

			sed -r \
				-e 's!%%WORDPRESS_VERSION%%!'"$current"'!g' \
				-e 's!%%WORDPRESS_SHA1%%!'"$sha1"'!g' \
				-e 's!%%PHP_VERSION%%!'"$phpVersion"'!g' \
				-e 's!%%VARIANT%%!'"$variant"'!g' \
				-e 's!%%WORDPRESS_CLI_VERSION%%!'"$cliVersion"'!g' \
				-e 's!%%WORDPRESS_CLI_SHA512%%!'"$cliSha512"'!g' \
				-e 's!%%VARIANT_EXTRAS%%!'"$extras"'!g' \
				-e 's!%%CMD%%!'"$cmd"'!g' \
				"Dockerfile-${base}.template" > "$dir/Dockerfile"

			cp -a "$entrypoint" "$dir/docker-entrypoint.sh"
		)

		travisEnv+='\n  - VARIANT='"$dir"
	done
done

travis="$(awk -v 'RS=\n\n' '$1 == "env:" { $0 = "env:'"$travisEnv"'" } { printf "%s%s", $0, RS }' .travis.yml)"
echo "$travis" > .travis.yml
