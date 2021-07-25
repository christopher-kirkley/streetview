const renderImage = (data) => {
	const imageData = data['img'];
	const img = document.getElementById('img')
	img.src = `data:image/jpeg;base64,${imageData}`
}

const renderError = () => {
	const error = document.getElementById('error');
	error.innerHTML = 'error';
}

const getStreetViewMetadata = async function() {
	const spinner = document.getElementById('spinner');
	spinner.style = "display:block";
	const resp = await fetch('street.php');
	const data = await resp.json();
	if (data['status'] == 'ok') {
		renderImage(data);
		spinner.style = "display:none";
	} if (data['status'] == 'error') {
		console.log(data['coord']);
		renderError();
	}

}


document.addEventListener('DOMContentLoaded', getStreetViewMetadata)
