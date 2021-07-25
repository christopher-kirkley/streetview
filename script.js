
const getRandomCoord = async function() {
  const url = "https://api.3geonames.org/?randomland=yes&json=1";
	const proxy = 'proxy.php?url=' + url;
	const resp = await fetch(proxy, { method: 'GET' });
	const data = await resp.json();
	const lat = data['nearest']['latt'];
	const long = data['nearest']['longt'];
	return (lat, long);
}

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
	const resp = await fetch('street.php');
	console.log(resp)
	const data = await resp.json();
	console.log(data)
	if (data['status'] == 'ok') {
		renderImage(data);
	} if (data['status'] == 'error') {
		console.log(data['coord']);
		renderError();
	}

}




document.addEventListener('click', getStreetViewMetadata)
// document.addEventListener('click', getRandomCoord);
