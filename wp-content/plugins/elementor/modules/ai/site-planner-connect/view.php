<?php

namespace Elementor\Modules\Ai\SitePlannerConnect;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
	href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap"
	rel="stylesheet"><?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
<style>
	#wpwrap {
		display: none;
	}

	.site-planner-consent {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 99999; /* above  admin top bar */
		width: 100%;
		background-color: #fff;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		height: 100vh;
	}

	.site-planner-consent-title {
		color: #0C0D0E;
		text-align: center;
		/* typography/h4 */
		font-family: Roboto, sans-serif;
		font-size: 32px;
		font-style: normal;
		font-weight: 700;
		line-height: 123.5%;
		letter-spacing: 0.25px;
	}

	.site-planner-consent-description {
		width: 393px;
		color: #69727D;
		text-align: center;
		/* typography/body1 */
		font-family: Roboto, sans-serif;
		font-size: 16px;
		font-style: normal;
		font-weight: 400;
		line-height: 150%; /* 24px */
		letter-spacing: 0.15px;
	}

	.site-planner-consent-connect-names {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		width: 500px;
	}

	.site-planner-consent-connect-names div {
		width: 50%;
		text-align: center;
	}

	.site-planner-consent button {
		cursor: pointer;
		border: none;
		display: flex;
		width: 387px;
		padding: 8px 22px;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		border-radius: 4px;
		background: #F0ABFC;
		color: #0C0D0E;
		font-feature-settings: 'liga' off, 'clig' off;

		/* components/button/button-large */
		font-family: Roboto, sans-serif;
		font-size: 16px;
		font-style: normal;
		font-weight: 500;
		line-height: 26px; /* 162.5% */
		letter-spacing: 0.46px;
	}

	.site-planner-consent .generating-results {
		display: none;
		padding: 8px 16px;
		margin: 0 32px;
	}

	.site-planner-consent .generating-results.error {
		display: block;
		background: rgb(253, 236, 236);
	}
</style>
<div class="site-planner-consent">
	<h1 class="site-planner-consent-title">
		%title%
	</h1>
	<div style="height: 20px"></div>
	<p class="site-planner-consent-description">
		%description%
	</p>
	<div style="height: 40px"></div>
	<svg width="287" height="40" viewBox="0 0 287 40" fill="none" xmlns="http://www.w3.org/2000/svg">
		<line x1="16.5" y1="22.5" x2="271.5" y2="22.5" stroke="#69727D" stroke-linecap="round" stroke-linejoin="round"
				stroke-dasharray="2 4"/>
		<circle cx="145.623" cy="22" r="11.5" fill="white" stroke="#69727D"/>
		<path fill-rule="evenodd" clip-rule="evenodd"
				d="M147.977 19.6467C148.172 19.842 148.172 20.1586 147.977 20.3538L143.977 24.3538C143.782 24.5491 143.465 24.5491 143.27 24.3538C143.074 24.1586 143.074 23.842 143.27 23.6467L147.27 19.6467C147.465 19.4515 147.782 19.4515 147.977 19.6467Z"
				fill="#69727D"/>
		<path
			d="M149.691 18.1948L149.402 17.9058C148.377 16.8804 146.714 16.8804 145.689 17.9058L145.002 18.5922C144.807 18.7875 144.491 18.7875 144.295 18.5922C144.1 18.397 144.1 18.0804 144.295 17.8851L144.982 17.1987C146.398 15.7827 148.693 15.7827 150.109 17.1987L150.398 17.4877C151.814 18.9036 151.814 21.1993 150.398 22.6153L149.712 23.3017C149.517 23.497 149.2 23.497 149.005 23.3017C148.81 23.1065 148.81 22.7899 149.005 22.5946L149.691 21.9082C150.717 20.8828 150.717 19.2202 149.691 18.1948Z"
			fill="#69727D"/>
		<path
			d="M141.529 22.0658C140.503 23.0912 140.503 24.7538 141.529 25.7792L141.818 26.0682C142.843 27.0936 144.506 27.0936 145.531 26.0682L146.218 25.3818C146.413 25.1865 146.73 25.1865 146.925 25.3818C147.12 25.577 147.12 25.8936 146.925 26.0889L146.238 26.7753C144.822 28.1913 142.527 28.1913 141.111 26.7753L140.822 26.4863C139.406 25.0704 139.406 22.7747 140.822 21.3587L141.508 20.6723C141.703 20.477 142.02 20.477 142.215 20.6723C142.411 20.8675 142.411 21.1841 142.215 21.3794L141.529 22.0658Z"
			fill="#69727D"/>
		<rect x="247" width="40" height="40" rx="20" fill="#F3F3F4"/>
		<g clip-path="url(#clip0_7635_41076)">
			<path fill-rule="evenodd" clip-rule="evenodd"
					d="M257.022 26.6668C255.704 24.6934 255 22.3734 255 20C255 16.8174 256.264 13.7652 258.515 11.5147C260.765 9.26428 263.817 8 267 8C269.373 8 271.693 8.70379 273.667 10.0224C275.64 11.3409 277.178 13.2151 278.087 15.4078C278.995 17.6005 279.232 20.0133 278.769 22.3411C278.306 24.6688 277.164 26.807 275.485 28.4853C273.807 30.1635 271.669 31.3064 269.341 31.7694C267.013 32.2324 264.601 31.9948 262.408 31.0865C260.215 30.1783 258.341 28.6402 257.022 26.6668ZM264 14.9996H262.001V24.9999H264V14.9996ZM271.999 14.9996H266V16.9993H271.999V14.9996ZM271.999 18.999H266V20.9987H271.999V18.999ZM271.999 23.0002H266V24.9999H271.999V23.0002Z"
					fill="#0C0D0E"/>
		</g>
		<rect width="40" height="40" rx="20" fill="#F3F3F4"/>
		<path fill-rule="evenodd" clip-rule="evenodd"
				d="M20.0004 10.0156C14.4944 10.0156 10.0156 14.494 10.0156 19.9996C10.0156 25.5053 14.4944 29.9844 20.0004 29.9844C25.5056 29.9844 29.9844 25.5053 29.9844 19.9996C29.9844 14.4947 25.5056 10.0156 20.0004 10.0156ZM11.1616 19.9996C11.1616 18.7184 11.4367 17.5017 11.927 16.4031L16.1431 27.9539C13.1948 26.5215 11.1616 23.4984 11.1616 19.9996ZM20.0004 28.8387C19.1327 28.8387 18.2954 28.7106 17.5032 28.4785L20.1549 20.7731L22.8725 28.2154C22.8898 28.2589 22.9115 28.2992 22.9353 28.3372C22.0167 28.6607 21.0292 28.8387 20.0004 28.8387ZM21.218 15.856C21.7501 15.8279 22.2293 15.7715 22.2293 15.7715C22.7058 15.7153 22.65 15.0158 22.1733 15.0438C22.1733 15.0438 20.7415 15.156 19.8176 15.156C18.9495 15.156 17.4894 15.0438 17.4894 15.0438C17.0133 15.0158 16.9579 15.744 17.4336 15.7715C17.4336 15.7715 17.8845 15.8277 18.3602 15.856L19.7373 19.6286L17.8034 25.4297L14.5851 15.8564C15.1178 15.8283 15.5968 15.7721 15.5968 15.7721C16.0725 15.7159 16.0169 15.016 15.54 15.0445C15.54 15.0445 14.1088 15.1564 13.1843 15.1564C13.0178 15.1564 12.823 15.1521 12.6157 15.1457C14.1954 12.7459 16.9123 11.1617 20.0004 11.1617C22.3018 11.1617 24.3964 12.0416 25.9689 13.4816C25.9302 13.4797 25.8937 13.4748 25.854 13.4748C24.9861 13.4748 24.3695 14.2309 24.3695 15.0434C24.3695 15.7715 24.789 16.388 25.2377 17.1159C25.5741 17.7051 25.9662 18.4613 25.9662 19.5537C25.9662 20.3102 25.6758 21.1882 25.2936 22.4107L24.4121 25.3566L21.218 15.856ZM24.4435 27.6389L27.1431 19.8337C27.6481 18.573 27.8152 17.5647 27.8152 16.6679C27.8152 16.343 27.7937 16.0404 27.7557 15.7591C28.4466 17.018 28.8391 18.4629 28.8386 19.9998C28.8386 23.2602 27.0708 26.1068 24.4435 27.6389Z"
				fill="#0C0D0E"/>
		<defs>
			<clipPath id="clip0_7635_41076">
				<rect width="24" height="24" fill="white" transform="translate(255 8)"/>
			</clipPath>
		</defs>
	</svg>

	<div class="site-planner-consent-connect-names">
		<div>%domain%</div>
		<div>%app_name%</div>
	</div>
	<div style="height: 40px"></div>

	<button class="site-planner-consent-button" onclick="sendPassword()">
		%cta%
	</button>

	<div style="height: 40px"></div>

	<div class="generating-results"></div>
</div>
<script>
	const generatingResults = document.querySelector(".generating-results");

	const hideAdminUi = () => {
		document.body.append(document.querySelector(".site-planner-consent"))
	}

	const sendPassword = () => {
		generatingResults.classList.remove("error");

		fetch(`${ wpApiSettings.root}wp/v2/users/me/application-passwords`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-WP-Nonce": wpApiSettings.nonce,
			},
			body: JSON.stringify({
				name: "Site Planner Connect"
			})
		})
			.then(response => response.json())
			.then(data => {
				window.opener.postMessage({
					type: "app_password",
					details: {
						userLogin: data.user_login,
						appPassword: data.password,
						uuid: data.uuid,
						created: data.created
					}
				}, '%safe_origin%');
				window.close();
			})
			.catch(error => {
				console.error("Error:", error);
				generatingResults.classList.add("error");
				generatingResults.innerText = "Error generating password: " + error;
			});
	}

	hideAdminUi();
</script>
