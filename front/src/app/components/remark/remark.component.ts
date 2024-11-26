import { AfterViewInit, Component, Input, OnDestroy } from '@angular/core';

@Component({
  selector: 'app-remark',
  standalone: true,
  templateUrl: './remark.component.html',
  styleUrls: ['./remark.component.css'],
})
export class RemarkComponent implements AfterViewInit, OnDestroy {
  @Input() siteId: string = 'localhost'; // Configura tu SITE_ID
  @Input() pageId: string = ''; // ID de la página donde se usarán los comentarios
  //@Input() category: string = ''; // Categoria, sección o tipo de publicación

  private remarkContainerId: string = ''; // ID único para cada contenedor de comentarios

  ngAfterViewInit(): void {
    if (!this.pageId) {
      console.error('pageId es requerido para usar Remark42');
      return;
    }

    this.remarkContainerId = `remark42-${this.pageId}`;
    this.initRemark();
  }

  private initRemark(): void {
    const remarkContainer = document.createElement('div');
    remarkContainer.id = this.remarkContainerId;

    const targetElement = document.getElementById('remark42');
    if (!targetElement) {
      console.error('No se encontró el contenedor principal para Remark42.');
      return;
    }
    targetElement.innerHTML = ''; // Limpia cualquier contenido previo
    targetElement.appendChild(remarkContainer);

    const script = document.createElement('script');
    script.src = 'http://localhost:8080/web/embed.js';
    script.async = true;
    script.defer = true;

    // Configuración para Remark42
    script.dataset['siteId'] = this.siteId;
    //script.dataset['url'] = `${window.location.origin}/planta/${this.pageId}`;
    script.dataset['url'] = `${window.location.origin}/publicacion/${this.pageId}`;
    //script.dataset['url'] = `${window.location.origin}/${this.category}/${this.pageId}`;
    remarkContainer.appendChild(script);
  }

  ngOnDestroy(): void {
    const remarkContainer = document.getElementById(this.remarkContainerId);
    if (remarkContainer) {
      remarkContainer.remove();
    }
  }
}