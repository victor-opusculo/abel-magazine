 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state =
    {
        allowed_mime_types: '',
        availableEditions: [],
        availableLanguages: [],
        waiting: false,
        id: null,
        edition_id: null,
        title: '',
        authors: '[]',
        resume: '',
        keywords: '',
        language: '',
        articleFile: null,
        authorsArr: [],
        lang: {}
    };

    const methods = 
    {
        changeField(e)
        {
            this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        setFile(e)
        {
            this.render({ ...this.render, articleFile: e.target.files[0] });
        },

        addAuthor(e)
        {
            const newArr = [ ...this.state.authorsArr, "" ];
            this.render({ ...this.state, authorsArr: newArr, authors: JSON.stringify(newArr) });
        },

        changeAuthor(e)
        {
            this.state.authorsArr[e.target.getAttribute('data-index')] = e.target.value;
            this.render({ ...this.state, authors: JSON.stringify(this.state.authorsArr) });
        },

        deleteAuthor(e)
        {
            const index = Number(e.target.getAttribute('data-index'));
            const newArr = this.state.authorsArr.filter((name, idx) => idx != index);
            this.render({ ...this.state, authorsArr: newArr, authors: JSON.stringify(newArr) });
        },

        create(formData)
        {
            import(AbelMagazine.functionUrl('/submitter/panel/articles'))
            .then(module => module.create(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(([ret, json]) =>
            {
                if (json.success && json.newId)
                    window.location.href = AbelMagazine.Helpers.URLGenerator.generatePageUrl(`/submitter/panel/articles/${json.newId}`);
                else
                    this.render({ ...this.state, waiting: false });
            })
            .catch(rs => (this.render({ ...this.state, waiting: false }), console.error(rs), AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, this.state.lang.forms.errorSubmittingArticle)));
        },

        edit(formData)
        {
            import(AbelMagazine.functionUrl(`/submitter/panel/articles/${this.state.id}`))
            .then(module => module.edit(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(_ => this.render({ ...this.state, waiting: false }))
            .catch(rs => (this.render({ ...this.state, waiting: false }), console.error(rs), AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, this.state.lang.forms.errorSubmittingArticle)));
        },

        submit(e)
        {
            e.preventDefault();

            if (!this.state.authorsArr?.length)
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, this.state.lang.forms.mustHaveOneAuthor);
                return;
            }

            this.render({ ...this.state, waiting: true });

            const fd = new FormData();

            for (const field in this.state)
                if (!['lang', 'availableEditions', 'availableLanguages', 'allowed_mime_types', 'waiting', 'articleFile', 'authorsArr'].includes(field))
                    fd.append("articles:" + field, this.state[field]);

            if (this.state.articleFile)
                fd.append('file_article_nid', this.state.articleFile);

            if (this.state.id)
                this.edit(fd);
            else
                this.create(fd);
        }
    };

    function setup()
    {
        this.render(
        { ...this.state, 
            lang: JSON.parse(this.getAttribute('langJson')),
            available_editions: JSON.parse(this.getAttribute('availableEditions')), 
            available_languages: JSON.parse(this.getAttribute('availableLanguages')),
            authorsArr: JSON.parse(this.getAttribute('authors') || '[]')
        });
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.magazineEdition}`}, [
        h("select", {"name": `edition_id`, "onchange": this.changeField.bind(this), "required": ``}, [
          h("option", {"selected": !state.edition_id, "value": ``}, `-- ${state.lang.forms.select} --`),
          ((state.available_editions).map((edit) => (h("option", {"value": `${edit.id}`, "selected": state.edition_id == edit.id}, `${edit.title}`))))
        ])
      ]),
      h("hr", {}, ""),
      h("ext-label", {"label": `${state.lang.forms.title}`}, [
        h("input", {"type": `text`, "name": `title`, "maxlength": `140`, "required": ``, "class": `w-full`, "onchange": this.changeField.bind(this), "value": state.title}, "")
      ]),
      h("div", {"class": `ml-2`}, [
        h("label", {}, `${state.lang.forms.authors}`),
        h("ol", {"class": `list-decimal pl-4`}, [
          ((state.authorsArr).map((name, idx) => (h("li", {"class": `mb-1`}, [
            h("input", {"type": `text`, "class": `w-[calc(100%-64px)]`, "required": ``, "data-index": idx, "onchange": this.changeAuthor.bind(this), "value": name}, ""),
            h("button", {"type": `button`, "class": `btn min-w-0 ml-2`, "data-index": idx, "onclick": this.deleteAuthor.bind(this)}, `×`)
          ]))))
        ]),
        h("button", {"type": `button`, "class": `btn`, "onclick": this.addAuthor.bind(this)}, `${state.lang.forms.add}`)
      ]),
      h("ext-label", {"label": `${state.lang.forms.resume}`, "linebreak": `1`}, [
        h("textarea", {"name": `resume`, "class": `w-full`, "rows": `8`, "onchange": this.changeField.bind(this), "value": state.resume, "required": ``}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.keywords}`}, [
        h("input", {"type": `text`, "name": `keywords`, "maxlength": `140`, "required": ``, "class": `w-full`, "onchange": this.changeField.bind(this), "value": state.keywords}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.language}`}, [
        h("select", {"name": `language`, "onchange": this.changeField.bind(this), "required": ``}, [
          h("option", {"selected": Boolean(state.language), "value": ``}, `-- ${state.lang.forms.select} --`),
          ((state.available_languages).map((lang) => (h("option", {"value": `${lang.code}`, "selected": state.language == lang.code}, `${lang.label}`))))
        ])
      ]),
      h("ext-label", {"label": `${state.lang.forms.file}`}, [
        h("input", {"type": `file`, "accept": `${state.allowed_mime_types}`, "name": `file`, "class": `file:btn`, "required": !Boolean(this.state.id), "onchange": this.setFile.bind(this)}, "")
      ]),
      h("p", {"class": `text-red-500 font-bold`}, `${state.lang.forms.notIddedFileUploadWarning}`),
      h("div", {"class": `mt-4 text-center`}, [
        h("button", {"type": `submit`, "class": `btn`, "disabled": state.waiting}, `${state.lang.forms.save}`)
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
