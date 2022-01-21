@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Videos</h3>
      </div>
      <div class="float-right">

      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-4">
        <div class="gallery">
        <div class="single-video">
          <figure>
            <iframe frameborder="0" src="https://www.youtube.com/embed/9ZwOYiDy0CA" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </figure>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('styles')
  <style media="screen">
  /* taking care of responsive layout */
  .gallery {
    display: flex;
    flex-wrap: wrap;
    margin: 0.5rem;
  }

  .single-video {
    width: 10rem;
    flex-grow: 1;
    margin: 0.5rem;
  }

  /* taking care of the video aspect-ratio */
  figure {
    position: relative;
    padding: 0 0 56.25% 0;
  }

  figure iframe {
    position: absolute;
    width: 100%;
    height: 100%;
  }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
  ;

// YouTube sintaxis for embedded videos and images

const ytVideoPrefix = `https://www.youtube.com/embed/`
const ytImagePathPrefix = `https://i.ytimg.com/vi/`
const ytImagePathSufix = `/hqdefault.jpg`

// Array of Videos

let arrVideos = [
{name: 'Cat Man Do', data: 'w0ffwDYo00Q'},
{name: 'Let Me In!', data: '4rb8aOzy9t4'},
{name: 'TV Dinner', data: 's13dLaTIHSg'},
{name: 'Cat & Mouse', data: 'BWIPZvwcnX8'},
{name: 'Feed Me', data: 'Te4wx4jtiEA'},
{name: 'Crazy Time', data: 'l5ODwR6FPRQ'},
{name: 'Bed Sheets', data: 'P3y8vc-3iVU'},
{name: 'Box Clever', data: 'ZpCl5O6tTv8'},
{name: 'Let Me Out!', data: 'HDzkaJOT_KI'},
{name: 'Mirror Mirror', data: 'G5FUH3eoizc'},
{name: 'Window Pain', data: 'XrivBjlv6Mw'},
{name: 'Sticky Tape', data: 'tV3SWjrt2rE'}
]

// Current video

let currentVideo = document.getElementById('current-video')
currentVideo.src = `${ytVideoPrefix}${arrVideos[0].data}`

// Add .gallery__items to .gallery

let gallery = document.querySelector('.gallery')
gallery.innerHTML = ``

for (let i = 0; i < arrVideos.length; i++) {
gallery.innerHTML += `
  <div class="gallery__item" data="${arrVideos[i].data}">
    <img class="gallery__item__img" src="${ytImagePathPrefix}${arrVideos[i].data}${ytImagePathSufix}">
    <span class="gallery__item__span">${arrVideos[i].name}</span>
  </div>`
}

// Add event listeners

gallery.addEventListener('click', (e) => {
// When click on .gallery__item element
if (e.target.classList.contains('gallery__item')) {
  currentVideo.src = `${ytVideoPrefix}${e.target.getAttribute('data')}`
}
// When click on .gallery__item__img element
if (e.target.classList.contains('gallery__item__img')) {
  let data = e.target.src
  data = data.replace(ytImagePathPrefix, '')
  data = data.replace(ytImagePathSufix, '')
  currentVideo.src = `${ytVideoPrefix}${data}`
}
// When click on .gallery__item__span element
if (e.target.classList.contains('gallery__item__span')) {
  console.log(e.target.innerText)
  for (let i = 0; i < arrVideos.length; i++) {
    if (arrVideos[i].name === e.target.innerText) {
      currentVideo.src = `${ytVideoPrefix}${arrVideos[i].data}`
    }
  }
}
})

  </script>
@endsection
