import { AfterViewInit, Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-remark',
  standalone: true,
  templateUrl: './remark.component.html',
  styleUrls: ['./remark.component.css'],
})
export class RemarkComponent implements AfterViewInit {
  constructor(
    @Inject(MAT_DIALOG_DATA) public data: { pageId: string },
    private dialogRef: MatDialogRef<RemarkComponent>
  ) { }

  ngAfterViewInit(): void {
    const script = document.createElement('script');
    script.src = 'http://localhost:8080/web/embed.js';
    script.async = true;
    script.defer = true;

    // Configuración global para Remark42
    (window as any).remark_config = {
      host: 'http://localhost:8080',
      site_id: 'localhost',
      url: `${window.location.origin}/publicacion/${this.data.pageId}`,
    };

    // Agregar el script dinámicamente después de que Angular complete el DOM
    document.body.appendChild(script);
  }

  closeDialog(): void {
    this.dialogRef.close();
  }
}
